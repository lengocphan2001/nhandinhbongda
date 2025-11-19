<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class SoccerApiService
{
    private const BASE_URL = 'https://api.soccersapi.com/v2.2';
    private const USER = 'Zr1NN';
    private const TOKEN = 'HlxImHi2Xa';
    
    /**
     * Get list of leagues from all pages (optimized with concurrent requests)
     */
    public function getLeagues()
    {
        return Cache::remember('leagues_list_all', 3600, function () {
            // First, get page 1 to know total pages
            $firstResponse = Http::get(self::BASE_URL . '/leagues/', [
                'user' => self::USER,
                'token' => self::TOKEN,
                't' => 'list',
                'page' => 1
            ]);
            
            if (!$firstResponse->successful()) {
                return ['data' => [], 'meta' => ['total' => 0, 'pages' => 1]];
            }
            
            $firstData = $firstResponse->json();
            if (!$firstData || !isset($firstData['data'])) {
                return ['data' => [], 'meta' => ['total' => 0, 'pages' => 1]];
            }
            
            $allLeagues = $firstData['data'];
            $totalPages = isset($firstData['meta']['pages']) ? (int) $firstData['meta']['pages'] : 1;
            
            // If only one page, return early
            if ($totalPages <= 1) {
                return [
                    'data' => $allLeagues,
                    'meta' => [
                        'total' => count($allLeagues),
                        'pages' => $totalPages
                    ]
                ];
            }
            
            // Fetch remaining pages concurrently (in batches to avoid overwhelming)
            $batchSize = 5; // Fetch 5 pages at a time
            for ($startPage = 2; $startPage <= $totalPages; $startPage += $batchSize) {
                $endPage = min($startPage + $batchSize - 1, $totalPages);
                
                $responses = Http::pool(function ($pool) use ($startPage, $endPage) {
                    for ($page = $startPage; $page <= $endPage; $page++) {
                        $pool->as("page_{$page}")->get(self::BASE_URL . '/leagues/', [
                            'user' => self::USER,
                            'token' => self::TOKEN,
                            't' => 'list',
                            'page' => $page
                        ]);
                    }
                });
                
                foreach ($responses as $key => $response) {
                    if ($response && $response->successful()) {
                        $data = $response->json();
                        if (isset($data['data'])) {
                            $allLeagues = array_merge($allLeagues, $data['data']);
                        }
                    }
                }
                
                // Small delay between batches
                if ($startPage + $batchSize <= $totalPages) {
                    usleep(50000); // 0.05 second delay between batches
                }
            }
            
            return [
                'data' => $allLeagues,
                'meta' => [
                    'total' => count($allLeagues),
                    'pages' => $totalPages
                ]
            ];
        });
    }
    
    /**
     * Get standings for a league by season_id
     */
    public function getStandings($seasonId)
    {
        return Cache::remember("standings_{$seasonId}", 300, function () use ($seasonId) {
            $response = Http::get(self::BASE_URL . '/leagues/', [
                'user' => self::USER,
                'token' => self::TOKEN,
                't' => 'standings',
                'season_id' => $seasonId
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        });
    }
    
    /**
     * Get team information by team_id
     */
    public function getTeamInfo($teamId)
    {
        return Cache::remember("team_info_{$teamId}", 3600, function () use ($teamId) {
            $response = Http::get(self::BASE_URL . '/teams/', [
                'user' => self::USER,
                'token' => self::TOKEN,
                't' => 'info',
                'id' => $teamId
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        });
    }
    
    /**
     * Get team logo URL by team_id
     */
    public function getTeamLogo($teamId)
    {
        $teamInfo = $this->getTeamInfo($teamId);
        
        if ($teamInfo && isset($teamInfo['data']['img'])) {
            return $teamInfo['data']['img'];
        }
        
        return null;
    }
    
    /**
     * Get multiple team logos concurrently
     */
    public function getTeamLogos(array $teamIds)
    {
        $logos = [];
        $uncachedIds = [];
        
        // Check cache first
        foreach ($teamIds as $teamId) {
            $cached = Cache::get("team_logo_{$teamId}");
            if ($cached !== null) {
                $logos[$teamId] = $cached;
            } else {
                $uncachedIds[] = $teamId;
            }
        }
        
        // Fetch uncached logos concurrently
        if (!empty($uncachedIds)) {
            $responses = Http::pool(function ($pool) use ($uncachedIds) {
                foreach ($uncachedIds as $teamId) {
                    $pool->as("team_{$teamId}")->get(self::BASE_URL . '/teams/', [
                        'user' => self::USER,
                        'token' => self::TOKEN,
                        't' => 'info',
                        'id' => $teamId
                    ]);
                }
            });
            
            foreach ($uncachedIds as $teamId) {
                $response = $responses["team_{$teamId}"] ?? null;
                if ($response && $response->successful()) {
                    $data = $response->json();
                    if (isset($data['data']['img'])) {
                        $logoUrl = $data['data']['img'];
                        $logos[$teamId] = $logoUrl;
                        Cache::put("team_logo_{$teamId}", $logoUrl, 3600);
                    }
                }
            }
        }
        
        return $logos;
    }
    
    /**
     * Search leagues by name
     */
    public function searchLeagues($query)
    {
        $leagues = $this->getLeagues();
        
        if (!$leagues || !isset($leagues['data'])) {
            return [];
        }
        
        $query = strtolower($query);
        
        return array_filter($leagues['data'], function ($league) use ($query) {
            return strpos(strtolower($league['name']), $query) !== false ||
                   strpos(strtolower($league['country_name'] ?? ''), $query) !== false;
        });
    }
    
    /**
     * Get fixtures/schedule for a specific date
     * Only returns matches with status_name = "Notstarted"
     * Fetches all pages
     */
    public function getFixtures($date, $utc = 7)
    {
        $cacheKey = "fixtures_{$date}_{$utc}";
        
        return Cache::remember($cacheKey, 300, function () use ($date, $utc) {
            // First, get page 1 to know total pages
            $firstResponse = Http::get(self::BASE_URL . '/fixtures/', [
                'user' => self::USER,
                'token' => self::TOKEN,
                't' => 'schedule',
                'include' => 'stats,odds,odds_prematch',
                'utc' => $utc,
                'd' => $date,
                'page' => 1
            ]);
            
            if (!$firstResponse->successful()) {
                return ['data' => [], 'meta' => ['total' => 0, 'pages' => 1]];
            }
            
            $firstData = $firstResponse->json();
            if (!$firstData || !isset($firstData['data'])) {
                return ['data' => [], 'meta' => ['total' => 0, 'pages' => 1]];
            }
            
            // Filter only "Notstarted" matches from first page
            $allFixtures = array_filter($firstData['data'], function ($fixture) {
                return isset($fixture['status_name']) && $fixture['status_name'] === 'Notstarted';
            });
            
            $totalPages = isset($firstData['meta']['pages']) ? (int) $firstData['meta']['pages'] : 1;
            
            // If only one page, return early
            if ($totalPages <= 1) {
                return [
                    'data' => array_values($allFixtures),
                    'meta' => [
                        'total' => count($allFixtures),
                        'pages' => $totalPages
                    ]
                ];
            }
            
            // Fetch remaining pages concurrently (in batches to avoid overwhelming)
            $batchSize = 5; // Fetch 5 pages at a time
            for ($startPage = 2; $startPage <= $totalPages; $startPage += $batchSize) {
                $endPage = min($startPage + $batchSize - 1, $totalPages);
                
                $responses = Http::pool(function ($pool) use ($startPage, $endPage, $date, $utc) {
                    for ($page = $startPage; $page <= $endPage; $page++) {
                        $pool->as("page_{$page}")->get(self::BASE_URL . '/fixtures/', [
                            'user' => self::USER,
                            'token' => self::TOKEN,
                            't' => 'schedule',
                            'include' => 'stats,odds,odds_prematch',
                            'utc' => $utc,
                            'd' => $date,
                            'page' => $page
                        ]);
                    }
                });
                
                foreach ($responses as $key => $response) {
                    if ($response && $response->successful()) {
                        $data = $response->json();
                        if (isset($data['data'])) {
                            // Filter only "Notstarted" matches
                            $filtered = array_filter($data['data'], function ($fixture) {
                                return isset($fixture['status_name']) && $fixture['status_name'] === 'Notstarted';
                            });
                            $allFixtures = array_merge($allFixtures, $filtered);
                        }
                    }
                }
                
                // Small delay between batches
                if ($startPage + $batchSize <= $totalPages) {
                    usleep(50000); // 0.05 second delay between batches
                }
            }
            
            return [
                'data' => array_values($allFixtures),
                'meta' => [
                    'total' => count($allFixtures),
                    'pages' => $totalPages
                ]
            ];
        });
    }
    
    /**
     * Get fixtures/results for a specific date (finished matches)
     * Only returns matches with status_name = "Finished"
     * Fetches all pages
     */
    public function getResults($date, $utc = 7)
    {
        $cacheKey = "results_{$date}_{$utc}";
        
        return Cache::remember($cacheKey, 300, function () use ($date, $utc) {
            // First, get page 1 to know total pages
            $firstResponse = Http::get(self::BASE_URL . '/fixtures/', [
                'user' => self::USER,
                'token' => self::TOKEN,
                't' => 'schedule',
                'include' => 'stats,odds,odds_prematch',
                'utc' => $utc,
                'd' => $date,
                'page' => 1
            ]);
            
            if (!$firstResponse->successful()) {
                return ['data' => [], 'meta' => ['total' => 0, 'pages' => 1]];
            }
            
            $firstData = $firstResponse->json();
            if (!$firstData || !isset($firstData['data'])) {
                return ['data' => [], 'meta' => ['total' => 0, 'pages' => 1]];
            }
            
            // Filter only "Finished" matches from first page
            $allFixtures = array_filter($firstData['data'], function ($fixture) {
                return isset($fixture['status_name']) && $fixture['status_name'] === 'Finished';
            });
            
            $totalPages = isset($firstData['meta']['pages']) ? (int) $firstData['meta']['pages'] : 1;
            
            // If only one page, return early
            if ($totalPages <= 1) {
                return [
                    'data' => array_values($allFixtures),
                    'meta' => [
                        'total' => count($allFixtures),
                        'pages' => $totalPages
                    ]
                ];
            }
            
            // Fetch remaining pages concurrently (in batches to avoid overwhelming)
            $batchSize = 5; // Fetch 5 pages at a time
            for ($startPage = 2; $startPage <= $totalPages; $startPage += $batchSize) {
                $endPage = min($startPage + $batchSize - 1, $totalPages);
                
                $responses = Http::pool(function ($pool) use ($startPage, $endPage, $date, $utc) {
                    for ($page = $startPage; $page <= $endPage; $page++) {
                        $pool->as("page_{$page}")->get(self::BASE_URL . '/fixtures/', [
                            'user' => self::USER,
                            'token' => self::TOKEN,
                            't' => 'schedule',
                            'include' => 'stats,odds,odds_prematch',
                            'utc' => $utc,
                            'd' => $date,
                            'page' => $page
                        ]);
                    }
                });
                
                foreach ($responses as $key => $response) {
                    if ($response && $response->successful()) {
                        $data = $response->json();
                        if (isset($data['data'])) {
                            // Filter only "Finished" matches
                            $filtered = array_filter($data['data'], function ($fixture) {
                                return isset($fixture['status_name']) && $fixture['status_name'] === 'Finished';
                            });
                            $allFixtures = array_merge($allFixtures, $filtered);
                        }
                    }
                }
                
                // Small delay between batches
                if ($startPage + $batchSize <= $totalPages) {
                    usleep(50000); // 0.05 second delay between batches
                }
            }
            
            return [
                'data' => array_values($allFixtures),
                'meta' => [
                    'total' => count($allFixtures),
                    'pages' => $totalPages
                ]
            ];
        });
    }
    
    /**
     * Get top scorers for a season
     */
    public function getTopScorers($seasonId)
    {
        return Cache::remember("top_scorers_{$seasonId}", 300, function () use ($seasonId) {
            $response = Http::get(self::BASE_URL . '/leaders/', [
                'user' => self::USER,
                'token' => self::TOKEN,
                't' => 'topscorers',
                'season_id' => $seasonId
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
        });
    }
}


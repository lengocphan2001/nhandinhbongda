<?php

namespace App\Http\Controllers;

use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ResultsController extends Controller
{
    protected $apiService;
    
    public function __construct(SoccerApiService $apiService)
    {
        $this->apiService = $apiService;
    }
    
    public function index(Request $request)
    {
        // Get date from request, default to today
        $dateParam = $request->get('d', Carbon::today()->format('Y-m-d'));
        
        // Validate date format
        try {
            $date = Carbon::parse($dateParam)->format('Y-m-d');
        } catch (\Exception $e) {
            $date = Carbon::today()->format('Y-m-d');
        }
        
        // Get results for the date
        $results = $this->apiService->getResults($date, 7);
        
        // Group results by league
        $groupedResults = [];
        if (isset($results['data']) && is_array($results['data'])) {
            foreach ($results['data'] as $fixture) {
                $leagueId = $fixture['league']['id'] ?? 'unknown';
                $leagueName = $fixture['league']['name'] ?? 'Unknown League';
                $countryName = $fixture['league']['country_name'] ?? '';
                
                if (!isset($groupedResults[$leagueId])) {
                    $groupedResults[$leagueId] = [
                        'league' => $fixture['league'],
                        'matches' => []
                    ];
                }
                
                $groupedResults[$leagueId]['matches'][] = $fixture;
            }
        }
        
        // Generate date navigation (7 days: 6 days ago to today)
        $dateNav = [];
        $today = Carbon::today();
        $vietnameseDays = [
            0 => 'Chủ Nhật',
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy'
        ];
        
        // Start from 6 days ago, end at today
        for ($i = 6; $i >= 0; $i--) {
            $navDate = $today->copy()->subDays($i);
            $dayOfWeek = $navDate->dayOfWeek;
            $dateNav[] = [
                'date' => $navDate->format('Y-m-d'),
                'display' => $navDate->format('d.m'),
                'day_name' => $i === 0 ? 'Hôm Nay' : $vietnameseDays[$dayOfWeek],
                'is_today' => $i === 0
            ];
        }
        
        return view('ket-qua', [
            'results' => $results['data'] ?? [],
            'groupedResults' => $groupedResults,
            'currentDate' => $date,
            'dateNav' => $dateNav,
            'meta' => $results['meta'] ?? []
        ]);
    }
}


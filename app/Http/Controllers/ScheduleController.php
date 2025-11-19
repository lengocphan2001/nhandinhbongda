<?php

namespace App\Http\Controllers;

use App\Services\SoccerApiService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    protected $apiService;
    
    public function __construct(SoccerApiService $apiService)
    {
        $this->apiService = $apiService;
    }
    
    public function index(Request $request)
    {
        // Get date from request, default to today
        $dateParam = $request->get('d', date('Y-m-d'));
        
        // Validate date format
        try {
            $date = Carbon::parse($dateParam)->format('Y-m-d');
        } catch (\Exception $e) {
            $date = date('Y-m-d');
        }
        
        // Get fixtures for the date
        $fixtures = $this->apiService->getFixtures($date, 7);
        
        // Group fixtures by league
        $groupedFixtures = [];
        if (isset($fixtures['data']) && is_array($fixtures['data'])) {
            foreach ($fixtures['data'] as $fixture) {
                $leagueId = $fixture['league']['id'] ?? 'unknown';
                $leagueName = $fixture['league']['name'] ?? 'Unknown League';
                $countryName = $fixture['league']['country_name'] ?? '';
                
                if (!isset($groupedFixtures[$leagueId])) {
                    $groupedFixtures[$leagueId] = [
                        'league' => $fixture['league'],
                        'matches' => []
                    ];
                }
                
                $groupedFixtures[$leagueId]['matches'][] = $fixture;
            }
        }
        
        // Generate date navigation (7 days: today + next 6 days)
        $dateNav = [];
        $today = Carbon::now();
        $vietnameseDays = [
            0 => 'Chủ Nhật',
            1 => 'Thứ Hai',
            2 => 'Thứ Ba',
            3 => 'Thứ Tư',
            4 => 'Thứ Năm',
            5 => 'Thứ Sáu',
            6 => 'Thứ Bảy'
        ];
        
        for ($i = 0; $i < 7; $i++) {
            $navDate = $today->copy()->addDays($i);
            $dayOfWeek = $navDate->dayOfWeek;
            $dateNav[] = [
                'date' => $navDate->format('Y-m-d'),
                'display' => $navDate->format('d.m'),
                'day_name' => $i === 0 ? 'Hôm Nay' : $vietnameseDays[$dayOfWeek],
                'is_today' => $i === 0
            ];
        }
        
        return view('lich-thi-dau', [
            'fixtures' => $fixtures['data'] ?? [],
            'groupedFixtures' => $groupedFixtures,
            'currentDate' => $date,
            'dateNav' => $dateNav,
            'meta' => $fixtures['meta'] ?? []
        ]);
    }
}


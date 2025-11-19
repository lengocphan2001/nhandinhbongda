<?php

namespace App\Http\Controllers;

use App\Services\SoccerApiService;
use Illuminate\Http\Request;

class StandingsController extends Controller
{
    protected $apiService;
    
    public function __construct(SoccerApiService $apiService)
    {
        $this->apiService = $apiService;
    }
    
    public function index(Request $request)
    {
        $leagues = $this->apiService->getLeagues();
        $selectedLeagueId = $request->get('league_id');
        
        // Default to league_id=583 if no league is selected
        if (!$selectedLeagueId) {
            $selectedLeagueId = 583;
        }
        
        $standings = null;
        $selectedLeague = null;
        
        // Get standings if league is selected
        if ($selectedLeagueId) {
            $selectedLeague = collect($leagues['data'] ?? [])->firstWhere('id', $selectedLeagueId);
            
            if ($selectedLeague && isset($selectedLeague['current_season_id']) && $selectedLeague['current_season_id']) {
                $standings = $this->apiService->getStandings($selectedLeague['current_season_id']);
            }
        }
        
        return view('bang-xep-hang-bong-da', [
            'leagues' => $leagues['data'] ?? [],
            'standings' => $standings,
            'selectedLeague' => $selectedLeague,
            'selectedLeagueId' => $selectedLeagueId
        ]);
    }
    
    public function searchLeagues(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            $leagues = $this->apiService->getLeagues();
            return response()->json($leagues['data'] ?? []);
        }
        
        $results = $this->apiService->searchLeagues($query);
        
        return response()->json(array_values($results));
    }
    
    /**
     * Get standings data via AJAX
     */
    public function getStandingsData(Request $request)
    {
        $leagueId = $request->get('league_id');
        
        if (!$leagueId) {
            return response()->json(['error' => 'League ID is required'], 400);
        }
        
        $leagues = $this->apiService->getLeagues();
        $selectedLeague = collect($leagues['data'] ?? [])->firstWhere('id', $leagueId);
        
        if (!$selectedLeague) {
            return response()->json(['error' => 'League not found'], 404);
        }
        
        $standings = null;
        
        if (isset($selectedLeague['current_season_id']) && $selectedLeague['current_season_id']) {
            $standings = $this->apiService->getStandings($selectedLeague['current_season_id']);
        }
        
        return response()->json([
            'league' => $selectedLeague,
            'standings' => $standings
        ]);
    }
}


<?php

namespace App\Http\Controllers;

use App\Services\SoccerApiService;
use Illuminate\Http\Request;

class TopScorersController extends Controller
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
        
        $topScorers = null;
        $selectedLeague = null;
        
        // Get top scorers if league is selected
        if ($selectedLeagueId) {
            $selectedLeague = collect($leagues['data'] ?? [])->firstWhere('id', $selectedLeagueId);
            
            if ($selectedLeague && isset($selectedLeague['current_season_id']) && $selectedLeague['current_season_id']) {
                $topScorers = $this->apiService->getTopScorers($selectedLeague['current_season_id']);
            }
        }
        
        return view('top-ghi-ban', [
            'leagues' => $leagues['data'] ?? [],
            'topScorers' => $topScorers,
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
     * Get top scorers data via AJAX
     */
    public function getTopScorersData(Request $request)
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
        
        $topScorers = null;
        
        if (isset($selectedLeague['current_season_id']) && $selectedLeague['current_season_id']) {
            $topScorers = $this->apiService->getTopScorers($selectedLeague['current_season_id']);
        }
        
        return response()->json([
            'league' => $selectedLeague,
            'topScorers' => $topScorers
        ]);
    }
}


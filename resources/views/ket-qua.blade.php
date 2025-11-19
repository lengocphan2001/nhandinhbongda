@extends('layouts.app')

@section('title', 'Kết Quả Bóng Đá Hôm Nay, KQ Bóng Đá Anh, C1, C2, La Liga - XOILAC TV')

@section('content')
<div class="bg-gray-800 rounded-lg p-6">

    <!-- Date Navigation -->
    <div class="mb-6 flex gap-2 items-center overflow-x-auto pb-2">
        @foreach($dateNav as $nav)
        <a href="{{ route('ket-qua', ['d' => $nav['date']]) }}" 
           class="px-4 py-2 rounded text-sm font-medium whitespace-nowrap transition-colors {{ $nav['date'] === $currentDate ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
            @if($nav['is_today'])
                Hôm Nay
            @else
                {{ $nav['day_name'] }}
            @endif
            {{ $nav['display'] }}
        </a>
        @endforeach
        <button class="px-3 py-2 text-gray-400 hover:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
    
    <!-- League List Link -->
    <div class="mb-4">
        <a href="#" class="text-green-400 hover:text-green-300 text-sm">Danh sách giải đấu</a>
    </div>
    
    <!-- Results Section -->
    <div class="space-y-6">
        @if(empty($groupedResults))
            <div class="text-center py-12 text-gray-400">
                <p>Không có kết quả nào vào ngày {{ \Carbon\Carbon::parse($currentDate)->format('d/m/Y') }}</p>
            </div>
        @else
            @foreach($groupedResults as $leagueId => $leagueGroup)
                @php
                    $league = $leagueGroup['league'];
                    $matches = $leagueGroup['matches'];
                    $countryName = $league['country_name'] ?? '';
                    $leagueName = $league['name'] ?? 'Unknown League';
                @endphp
                
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-lg font-bold text-white">
                            {{ $countryName ? $countryName . ': ' : '' }}{{ $leagueName }}
                            @if(count($matches) > 0)
                                <span class="text-gray-400 text-sm font-normal">({{ count($matches) }})</span>
                            @endif
                        </h2>
                        <a href="{{ route('bang-xep-hang', ['league_id' => $leagueId]) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-sm text-white rounded transition-colors">
                            <span>BXH</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <div class="bg-gray-900 rounded-lg overflow-hidden">
                        <!-- Table Header -->
                        <div class="grid grid-cols-11 gap-2 bg-gray-700 px-4 py-2 text-xs font-semibold text-gray-300">
                            <div class="col-span-3">Trận đấu</div>
                            <div class="col-span-1 text-center">Tỷ số</div>
                            <div class="col-span-1 text-center">Hiệp 1</div>
                            <div class="col-span-2 text-end">Cược chấp</div>
                            <div class="col-span-2 text-end">Tài/Xỉu</div>
                            <div class="col-span-2 text-end">1X2</div>
                        </div>
                        
                        <!-- Matches -->
                        @foreach($matches as $match)
                            @php
                                $homeTeam = $match['teams']['home'] ?? [];
                                $awayTeam = $match['teams']['away'] ?? [];
                                $homeTeamName = $homeTeam['name'] ?? 'N/A';
                                $awayTeamName = $awayTeam['name'] ?? 'N/A';
                                $homeLogo = $homeTeam['img'] ?? null;
                                $awayLogo = $awayTeam['img'] ?? null;
                                
                                $matchTime = $match['time'] ?? [];
                                $matchDateTime = $matchTime['datetime'] ?? '';
                                $matchTimeStr = $matchTime['time'] ?? '';
                                
                                $scores = $match['scores'] ?? [];
                                $homeScore = $scores['home_score'] ?? '';
                                $awayScore = $scores['away_score'] ?? '';
                                $htScore = $scores['ht_score'] ?? '-';
                                $ftScore = $scores['ft_score'] ?? '-';
                                
                                // Parse HT score
                                $htHomeScore = '';
                                $htAwayScore = '';
                                if ($htScore && $htScore !== '-') {
                                    $htParts = explode('-', $htScore);
                                    if (count($htParts) == 2) {
                                        $htHomeScore = trim($htParts[0]);
                                        $htAwayScore = trim($htParts[1]);
                                    }
                                }
                                
                                // Parse odds_prematch data
                                $oddsPrematch = $match['odds_prematch'] ?? [];
                                $handicap = null;
                                $overUnder = null;
                                $odds1X2 = null;
                                
                                // Find Bet365 (id: 2) in each odds type
                                $bet365Id = 2;
                                
                                if (is_array($oddsPrematch) && !empty($oddsPrematch)) {
                                    foreach ($oddsPrematch as $oddsType) {
                                        $oddsTypeId = $oddsType['id'] ?? null;
                                        $bookmakers = $oddsType['bookmakers'] ?? [];
                                        
                                        // Find Bet365 in bookmakers
                                        $bet365Bookmaker = null;
                                        foreach ($bookmakers as $bookmaker) {
                                            if (isset($bookmaker['id']) && $bookmaker['id'] == $bet365Id) {
                                                $bet365Bookmaker = $bookmaker;
                                                break;
                                            }
                                        }
                                        
                                        // If Bet365 not found, use first bookmaker
                                        if (!$bet365Bookmaker && !empty($bookmakers)) {
                                            $bet365Bookmaker = $bookmakers[0];
                                        }
                                        
                                        if ($bet365Bookmaker && isset($bet365Bookmaker['odds']['data'])) {
                                            $oddsData = $bet365Bookmaker['odds']['data'];
                                            
                                            // 1X2, Full Time Result (id: 1)
                                            if ($oddsTypeId == 1) {
                                                $odds1X2 = $oddsData;
                                            }
                                            // Asian Handicap (id: 3)
                                            elseif ($oddsTypeId == 3) {
                                                $handicap = $oddsData;
                                            }
                                            // Over/Under, Goal Line (id: 2)
                                            elseif ($oddsTypeId == 2) {
                                                // Over/Under has data as array
                                                if (is_array($oddsData) && !empty($oddsData)) {
                                                    $overUnder = $oddsData[0];
                                                } else {
                                                    $overUnder = $oddsData;
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp
                            
                            <div class="grid grid-cols-11 gap-2 px-4 py-3 border-b border-gray-700 hover:bg-gray-800 transition-colors">
                                <!-- Match Info -->
                                <div class="col-span-3 flex items-center gap-2">
                                    <div class="text-xs text-gray-400 mb-2">Kết thúc</div>
                                    <!-- Home Team -->

                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($homeLogo)
                                                <img src="{{ $homeLogo }}" alt="{{ $homeTeamName }}" class="w-5 h-5 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white" style="display: none;">{{ substr($homeTeamName, 0, 1) }}</div>
                                            @else
                                                <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white">{{ substr($homeTeamName, 0, 1) }}</div>
                                            @endif
                                            <span class="text-white text-sm">{{ $homeTeamName }}</span>
                                        </div>
                                        <!-- Away Team -->
                                        <div class="flex items-center gap-2">
                                            @if($awayLogo)
                                                <img src="{{ $awayLogo }}" alt="{{ $awayTeamName }}" class="w-5 h-5 object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white" style="display: none;">{{ substr($awayTeamName, 0, 1) }}</div>
                                            @else
                                                <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white">{{ substr($awayTeamName, 0, 1) }}</div>
                                            @endif
                                            <span class="text-white text-sm">{{ $awayTeamName }}</span>
                                        </div>
                                    </div>
                                    
                                </div>
                                
                                <!-- Score -->
                                <div class="col-span-1 text-center text-white text-sm flex flex-col items-center justify-center gap-1">
                                    @if($homeScore !== '' && $awayScore !== '')
                                        <div class="font-bold">{{ $homeScore }}</div>
                                        <div class="font-bold">{{ $awayScore }}</div>
                                    @else
                                        <div class="text-gray-400">-</div>
                                    @endif
                                </div>
                                
                                <!-- Half 1 -->
                                <div class="col-span-1 text-center text-gray-400 text-sm flex flex-col items-center justify-center gap-1">
                                    @if($htHomeScore !== '' && $htAwayScore !== '')
                                        <div>{{ $htHomeScore }}</div>
                                        <div>{{ $htAwayScore }}</div>
                                    @else
                                        <div>-</div>
                                    @endif
                                </div>
                                
                                <!-- Handicap Odds -->
                                <div class="col-span-2 text-xs">
                                    @if($handicap && is_array($handicap))
                                        @php
                                            $handicapValue = $handicap['handicap'] ?? '0';
                                            $homeOdds = $handicap['home'] ?? '-';
                                            $awayOdds = $handicap['away'] ?? '-';
                                        @endphp
                                        <div class="flex justify-end gap-2">
                                            <!-- Cột 1: Handicap value -->
                                            <div class="flex items-start">
                                                <span class="text-gray-300">{{ $handicapValue }}</span>
                                            </div>
                                            <!-- Cột 2: 2 dòng odds -->
                                            <div class="flex flex-col gap-1 items-start">
                                                <div>
                                                    <span class="text-green-400">{{ $homeOdds }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-green-400">{{ $awayOdds }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-end text-gray-500">-</div>
                                    @endif
                                </div>
                                
                                <!-- Over/Under Odds -->
                                <div class="col-span-2 text-xs">
                                    @if($overUnder && is_array($overUnder))
                                        @php
                                            $totalValue = $overUnder['handicap'] ?? '2.5';
                                            $overOdds = $overUnder['over'] ?? '-';
                                            $underOdds = $overUnder['under'] ?? '-';
                                        @endphp
                                        <div class="flex items-start justify-end gap-2">
                                            <!-- Cột 1: Handicap value -->
                                            <div class="flex items-end">
                                                <span class="text-gray-300">{{ $totalValue }}</span>
                                            </div>
                                            <!-- Cột 2: 2 dòng odds -->
                                            <div class="flex flex-col gap-1 items-start">
                                                <div>
                                                    <span class="text-green-400">{{ $overOdds }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-green-400">{{ $underOdds }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-end text-gray-500">-</div>
                                    @endif
                                </div>
                                
                                <!-- 1X2 Odds -->
                                <div class="col-span-2 text-xs">
                                    @if($odds1X2 && is_array($odds1X2))
                                        @php
                                            $homeWin = $odds1X2['home'] ?? '-';
                                            $draw = $odds1X2['draw'] ?? '-';
                                            $awayWin = $odds1X2['away'] ?? '-';
                                        @endphp
                                        <div class="flex flex-col gap-1 items-end">
                                            <div>
                                                <span class="text-green-400">{{ $homeWin }}</span>
                                            </div>
                                            <div>
                                                <span class="text-green-400">{{ $draw }}</span>
                                            </div>
                                            <div>
                                                <span class="text-green-400">{{ $awayWin }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-end text-gray-500">-</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection

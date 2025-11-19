@extends('layouts.app')

@section('title', 'Lịch Thi Đấu Bóng Đá Hôm Nay, LTĐ Bóng Đá Anh, C1, C2, La Liga - XOILAC TV')

@section('content')
<div class="bg-gray-800 rounded-lg p-3 sm:p-4 md:p-6 overflow-hidden">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-4 sm:mb-6 uppercase break-words">Lịch Thi Đấu Bóng Đá Hôm Nay, LTĐ Bóng Đá Anh, C1, C2, La Liga</h1>
    
    <!-- Date Navigation -->
    <div class="mb-4 sm:mb-6 flex gap-2 items-center overflow-x-auto pb-2 scrollbar-hide -mx-3 sm:-mx-4 md:-mx-6 px-3 sm:px-4 md:px-6">
        @foreach($dateNav as $nav)
        <a href="{{ route('lich-thi-dau', ['d' => $nav['date']]) }}" 
           class="px-3 sm:px-4 py-2 rounded text-xs sm:text-sm font-medium whitespace-nowrap flex-shrink-0 transition-colors {{ $nav['date'] === $currentDate ? 'bg-green-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">
            @if($nav['is_today'])
                Hôm Nay
            @else
                <span class="hidden sm:inline">{{ $nav['day_name'] }} </span>
            @endif
            {{ $nav['display'] }}
        </a>
        @endforeach
    </div>
    
    <!-- League List Link -->
    <div class="mb-4">
        <a href="#" class="text-green-400 hover:text-green-300 text-xs sm:text-sm">Danh sách giải đấu</a>
    </div>
    
    <!-- Matches Section -->
    <div class="space-y-4 sm:space-y-6">
        @if(empty($groupedFixtures))
            <div class="text-center py-8 sm:py-12 text-gray-400 text-sm sm:text-base">
                <p>Không có trận đấu nào vào ngày {{ \Carbon\Carbon::parse($currentDate)->format('d/m/Y') }}</p>
            </div>
        @else
            @foreach($groupedFixtures as $leagueId => $leagueGroup)
                @php
                    $league = $leagueGroup['league'];
                    $matches = $leagueGroup['matches'];
                    $countryName = $league['country_name'] ?? '';
                    $leagueName = $league['name'] ?? 'Unknown League';
                @endphp
                
                <div class="mb-4 sm:mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-0 mb-3">
                        <h2 class="text-base sm:text-lg font-bold text-white truncate pr-2">
                            <span class="truncate block">{{ $countryName ? $countryName . ': ' : '' }}{{ $leagueName }}</span>
                            @if(count($matches) > 0)
                                <span class="text-gray-400 text-xs sm:text-sm font-normal">({{ count($matches) }})</span>
                            @endif
                        </h2>
                        <a href="{{ route('bang-xep-hang', ['league_id' => $leagueId]) }}" 
                           class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 bg-green-600 hover:bg-green-700 text-xs sm:text-sm text-white rounded transition-colors flex-shrink-0 self-start sm:self-auto">
                            <span>BXH</span>
                            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </a>
                    </div>
                    
                    <div class="bg-gray-900 rounded-lg overflow-hidden">
                        <!-- Desktop Table Header (hidden on mobile) -->
                        <div class="hidden md:grid md:grid-cols-11 gap-2 bg-gray-700 px-4 py-2 text-xs font-semibold text-gray-300">
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
                            
                            <!-- Mobile Card Layout -->
                            <div class="md:hidden border-b border-gray-700 hover:bg-gray-800 transition-colors p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-xs text-gray-400">{{ $matchTimeStr }}</div>
                                    <div class="text-gray-400 text-sm">-</div>
                                </div>
                                
                                <!-- Teams -->
                                <div class="space-y-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        @if($homeLogo)
                                            <img src="{{ $homeLogo }}" alt="{{ $homeTeamName }}" class="w-5 h-5 object-contain flex-shrink-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" style="display: none;">{{ substr($homeTeamName, 0, 1) }}</div>
                                        @else
                                            <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">{{ substr($homeTeamName, 0, 1) }}</div>
                                        @endif
                                        <span class="text-white text-sm truncate min-w-0 flex-1">{{ $homeTeamName }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($awayLogo)
                                            <img src="{{ $awayLogo }}" alt="{{ $awayTeamName }}" class="w-5 h-5 object-contain flex-shrink-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" style="display: none;">{{ substr($awayTeamName, 0, 1) }}</div>
                                        @else
                                            <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">{{ substr($awayTeamName, 0, 1) }}</div>
                                        @endif
                                        <span class="text-white text-sm truncate min-w-0 flex-1">{{ $awayTeamName }}</span>
                                    </div>
                                </div>
                                
                                <!-- Odds - Mobile -->
                                <div class="grid grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <div class="text-gray-400 mb-1">Hiệp 1</div>
                                        <div class="text-gray-500">-</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-400 mb-1">Cược chấp</div>
                                        @if($handicap && is_array($handicap))
                                            @php
                                                $handicapValue = $handicap['handicap'] ?? '0';
                                                $homeOdds = $handicap['home'] ?? '-';
                                                $awayOdds = $handicap['away'] ?? '-';
                                            @endphp
                                            <div class="text-gray-300">{{ $handicapValue }}</div>
                                            <div class="text-green-400">{{ $homeOdds }} / {{ $awayOdds }}</div>
                                        @else
                                            <div class="text-gray-500">-</div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-gray-400 mb-1">Tài/Xỉu</div>
                                        @if($overUnder && is_array($overUnder))
                                            @php
                                                $totalValue = $overUnder['handicap'] ?? '2.5';
                                                $overOdds = $overUnder['over'] ?? '-';
                                                $underOdds = $overUnder['under'] ?? '-';
                                            @endphp
                                            <div class="text-gray-300">{{ $totalValue }}</div>
                                            <div class="text-green-400">{{ $overOdds }} / {{ $underOdds }}</div>
                                        @else
                                            <div class="text-gray-500">-</div>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($odds1X2 && is_array($odds1X2))
                                    @php
                                        $homeWin = $odds1X2['home'] ?? '-';
                                        $draw = $odds1X2['draw'] ?? '-';
                                        $awayWin = $odds1X2['away'] ?? '-';
                                    @endphp
                                    <div class="mt-3 pt-3 border-t border-gray-700">
                                        <div class="text-gray-400 text-xs mb-2">1X2</div>
                                        <div class="flex gap-4 text-green-400 text-xs">
                                            <div>1: {{ $homeWin }}</div>
                                            <div>X: {{ $draw }}</div>
                                            <div>2: {{ $awayWin }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Desktop Table Row -->
                            <div class="hidden md:grid md:grid-cols-11 gap-2 px-4 py-3 border-b border-gray-700 hover:bg-gray-800 transition-colors">
                                <!-- Match Info -->
                                <div class="col-span-3 flex items-center gap-2 min-w-0">
                                    <div class="text-xs text-gray-400 mb-2 hidden lg:block">{{ $matchTimeStr }}</div>
                                    <div class="flex-1 min-w-0">
                                        <!-- Home Team -->
                                        <div class="flex items-center gap-2 mb-1 min-w-0">
                                            @if($homeLogo)
                                                <img src="{{ $homeLogo }}" alt="{{ $homeTeamName }}" class="w-5 h-5 object-contain flex-shrink-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" style="display: none;">{{ substr($homeTeamName, 0, 1) }}</div>
                                            @else
                                                <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">{{ substr($homeTeamName, 0, 1) }}</div>
                                            @endif
                                            <span class="text-white text-sm truncate min-w-0">{{ $homeTeamName }}</span>
                                        </div>
                                        <!-- Away Team -->
                                        <div class="flex items-center gap-2 min-w-0">
                                            @if($awayLogo)
                                                <img src="{{ $awayLogo }}" alt="{{ $awayTeamName }}" class="w-5 h-5 object-contain flex-shrink-0" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0" style="display: none;">{{ substr($awayTeamName, 0, 1) }}</div>
                                            @else
                                                <div class="w-5 h-5 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">{{ substr($awayTeamName, 0, 1) }}</div>
                                            @endif
                                            <span class="text-white text-sm truncate min-w-0">{{ $awayTeamName }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Score -->
                                <div class="col-span-1 text-center text-gray-400 text-sm flex items-center justify-center">
                                    -
                                </div>
                                
                                <!-- Half 1 -->
                                <div class="col-span-1 text-center text-gray-400 text-sm flex items-center justify-center">
                                    -
                                </div>
                                
                                <!-- Handicap Odds -->
                                <div class="col-span-2 text-xs min-w-0">
                                    @if($handicap && is_array($handicap))
                                        @php
                                            $handicapValue = $handicap['handicap'] ?? '0';
                                            $homeOdds = $handicap['home'] ?? '-';
                                            $awayOdds = $handicap['away'] ?? '-';
                                        @endphp
                                        <div class="flex items-start justify-end gap-2">
                                            <!-- Cột 1: Handicap value -->
                                            <div class="flex items-start flex-shrink-0">
                                                <span class="text-gray-300 whitespace-nowrap">{{ $handicapValue }}</span>
                                            </div>
                                            <!-- Cột 2: 2 dòng odds -->
                                            <div class="flex flex-col gap-1 items-start min-w-0">
                                                <div class="text-green-400 truncate w-full">{{ $homeOdds }}</div>
                                                <div class="text-green-400 truncate w-full">{{ $awayOdds }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-end text-gray-500">-</div>
                                    @endif
                                </div>
                                
                                <!-- Over/Under Odds -->
                                <div class="col-span-2 text-xs min-w-0">
                                    @if($overUnder && is_array($overUnder))
                                        @php
                                            $totalValue = $overUnder['handicap'] ?? '2.5';
                                            $overOdds = $overUnder['over'] ?? '-';
                                            $underOdds = $overUnder['under'] ?? '-';
                                        @endphp
                                        <div class="flex items-start justify-end gap-2">
                                            <!-- Cột 1: Handicap value -->
                                            <div class="flex items-end flex-shrink-0">
                                                <span class="text-gray-300 whitespace-nowrap">{{ $totalValue }}</span>
                                            </div>
                                            <!-- Cột 2: 2 dòng odds -->
                                            <div class="flex flex-col gap-1 items-start min-w-0">
                                                <div class="text-green-400 truncate w-full">{{ $overOdds }}</div>
                                                <div class="text-green-400 truncate w-full">{{ $underOdds }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-end text-gray-500">-</div>
                                    @endif
                                </div>
                                
                                <!-- 1X2 Odds -->
                                <div class="col-span-2 text-xs min-w-0">
                                    @if($odds1X2 && is_array($odds1X2))
                                        @php
                                            $homeWin = $odds1X2['home'] ?? '-';
                                            $draw = $odds1X2['draw'] ?? '-';
                                            $awayWin = $odds1X2['away'] ?? '-';
                                        @endphp
                                        <div class="flex flex-col gap-1 items-end">
                                            <div class="text-green-400 truncate w-full text-right">{{ $homeWin }}</div>
                                            <div class="text-green-400 truncate w-full text-right">{{ $draw }}</div>
                                            <div class="text-green-400 truncate w-full text-right">{{ $awayWin }}</div>
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

@push('styles')
<style>
    /* Hide scrollbar but keep functionality */
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    /* Ensure text doesn't overflow */
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endpush
@endsection

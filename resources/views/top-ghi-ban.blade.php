@extends('layouts.app')

@section('title', 'Top Ghi Bàn #1 Danh Sách Vua Phá Lưới Mùa Giải 2025/2026 - XOILAC TV')

@section('content')
<div class="bg-gray-800 rounded-lg p-3 sm:p-4 md:p-6 overflow-hidden">
    <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-3 sm:mb-4 uppercase break-words">Top Ghi Bàn #1 Danh Sách Vua Phá Lưới Mùa Giải 2025/2026</h1>
    
    <p class="text-gray-300 mb-4 sm:mb-6 leading-relaxed text-sm sm:text-base">
        Top ghi bàn hay vua phá lưới là danh hiệu cao quý mà bất cứ cầu thủ nào cũng muốn chinh phục. Vậy những yếu tố nào hình thành nên một vua phá lưới? Ai là cầu thủ đang dẫn đầu top ghi bàn tại các giải đầu hàng đầu thế giới? Hãy cùng Xoilac điểm qua những chân sút thượng thặng này.
    </p>
    
    <!-- League Selector with Search -->
    <div class="mb-4 sm:mb-6">
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-stretch sm:items-start">
            <!-- Search Input -->
            <div class="relative w-full sm:max-w-md sm:flex-1 min-w-0">
                <input 
                    type="text" 
                    id="leagueSearch" 
                    placeholder="Tìm kiếm giải đấu..." 
                    class="w-full bg-gray-700 text-white px-3 sm:px-4 py-2 pr-10 rounded border border-gray-600 hover:border-green-500 focus:outline-none focus:border-green-500 transition-colors text-sm sm:text-base"
                    autocomplete="off"
                    value="{{ $selectedLeague ? $selectedLeague['name'] : '' }}"
                >
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <!-- Dropdown Results -->
                <div id="leagueDropdown" class="hidden absolute z-10 w-full mt-1 bg-gray-700 border border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <!-- Results will be populated here -->
                </div>
            </div>
            
            <!-- Select Dropdown -->
            <div class="relative w-full sm:w-auto sm:min-w-[200px] md:min-w-[250px]">
                <select 
                    id="leagueSelect" 
                    class="w-full sm:w-auto bg-gray-700 text-white px-3 sm:px-4 py-2 pr-8 rounded border border-gray-600 hover:border-green-500 focus:outline-none focus:border-green-500 transition-colors appearance-none cursor-pointer text-sm sm:text-base"
                >
                    <option value="">-- Chọn giải đấu --</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league['id'] }}" {{ $selectedLeagueId == $league['id'] ? 'selected' : '' }}>
                            {{ $league['name'] }} - {{ $league['country_name'] }}
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div id="selectedLeagueInfo" class="mt-2 text-xs sm:text-sm text-gray-400 truncate" style="display: {{ $selectedLeague ? 'block' : 'none' }};">
            @if($selectedLeague)
            Đang hiển thị: <span class="text-green-400 font-semibold inline-block max-w-full">{{ $selectedLeague['name'] }}</span>
            @if($selectedLeague['country_name'])
                <span class="text-gray-500">({{ $selectedLeague['country_name'] }})</span>
            @endif
            @endif
        </div>
    </div>
    
    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden text-center py-8 sm:py-12">
        <div class="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-green-500"></div>
        <p class="mt-4 text-gray-400 text-sm sm:text-base">Đang tải dữ liệu...</p>
    </div>
    
    <!-- Top Scorers Table Container -->
    <div id="topScorersContainer" class="overflow-x-auto -mx-3 sm:-mx-4 md:-mx-6 px-3 sm:px-4 md:px-6">
        @if($topScorers && isset($topScorers['data']) && !empty($topScorers['data']))
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm min-w-[600px]">
                    <thead>
                        <tr class="bg-green-600 text-white">
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold">#</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold">Tên cầu thủ</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold">Tên đội</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold">Bàn thắng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($topScorers['data'], 0, 50) as $scorer)
                            @php
                                $player = $scorer['player'] ?? [];
                                $team = $scorer['team'] ?? [];
                                $playerName = $player['name'] ?? 'N/A';
                                $teamName = $team['name'] ?? 'N/A';
                                $goals = $scorer['goals']['overall'] ?? 0;
                                $penalties = $scorer['penalties'] ?? null;
                                $position = $scorer['pos'] ?? $loop->iteration;
                            @endphp
                            <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-gray-300">{{ $position }}</td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 min-w-0">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">
                                            {{ substr($playerName, 0, 1) }}
                                        </div>
                                        <span class="text-white font-medium truncate min-w-0">{{ $playerName }}</span>
                                    </div>
                                </td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 min-w-0">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-5 h-5 sm:w-6 sm:h-6 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">
                                            {{ substr($teamName, 0, 1) }}
                                        </div>
                                        <span class="text-gray-300 truncate min-w-0">{{ $teamName }}</span>
                                    </div>
                                </td>
                                <td class="px-2 sm:px-4 py-2 sm:py-3 text-left">
                                    <span class="text-green-400 font-bold text-base sm:text-lg">
                                        {{ $goals }}
                                        @if($penalties !== null && $penalties > 0)
                                            <span class="text-gray-400 text-xs sm:text-sm font-normal">({{ $penalties }})</span>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 sm:py-12">
                <p class="text-gray-400 text-sm sm:text-base md:text-lg">Vui lòng chọn giải đấu để xem top ghi bàn</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('leagueSearch');
    const dropdown = document.getElementById('leagueDropdown');
    const leagueSelect = document.getElementById('leagueSelect');
    const topScorersContainer = document.getElementById('topScorersContainer');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const selectedLeagueInfo = document.getElementById('selectedLeagueInfo');
    let searchTimeout;
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`{{ route('api.top-scorers.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    dropdown.innerHTML = '';
                    if (data.length === 0) {
                        dropdown.innerHTML = '<div class="px-4 py-2 text-gray-400">Không tìm thấy kết quả</div>';
                    } else {
                        data.forEach(league => {
                            const item = document.createElement('div');
                            item.className = 'px-4 py-2 hover:bg-gray-600 cursor-pointer text-white';
                            item.textContent = `${league.name} - ${league.country_name || ''}`;
                            item.addEventListener('click', () => {
                                searchInput.value = league.name;
                                dropdown.classList.add('hidden');
                                leagueSelect.value = league.id;
                                loadTopScorers(league.id);
                            });
                            dropdown.appendChild(item);
                        });
                    }
                    dropdown.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }, 300);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    // League select change
    leagueSelect.addEventListener('change', function() {
        const leagueId = this.value;
        if (leagueId) {
            loadTopScorers(leagueId);
        }
    });
    
    // Load top scorers via AJAX
    function loadTopScorers(leagueId) {
        // Show loading
        loadingIndicator.classList.remove('hidden');
        topScorersContainer.innerHTML = '';
        selectedLeagueInfo.style.display = 'none';
        
        fetch(`{{ route('api.top-scorers') }}?league_id=${leagueId}`)
            .then(response => response.json())
            .then(data => {
                loadingIndicator.classList.add('hidden');
                
                if (data.error) {
                    topScorersContainer.innerHTML = `<div class="text-center py-12"><p class="text-red-400">${data.error}</p></div>`;
                    return;
                }
                
                // Update selected league info
                if (data.league) {
                    const countryName = data.league.country_name ? `(${data.league.country_name})` : '';
                    selectedLeagueInfo.innerHTML = `Đang hiển thị: <span class="text-green-400 font-semibold">${data.league.name}</span> <span class="text-gray-500">${countryName}</span>`;
                    selectedLeagueInfo.style.display = 'block';
                }
                
                // Render top scorers
                if (data.topScorers && data.topScorers.data && data.topScorers.data.length > 0) {
                    renderTopScorers(data.topScorers.data);
                } else {
                    topScorersContainer.innerHTML = '<div class="text-center py-12"><p class="text-gray-400 text-lg">Không có dữ liệu top ghi bàn</p></div>';
                }
            })
            .catch(error => {
                loadingIndicator.classList.add('hidden');
                topScorersContainer.innerHTML = '<div class="text-center py-12"><p class="text-red-400">Có lỗi xảy ra khi tải dữ liệu</p></div>';
                console.error('Error:', error);
            });
    }
    
    // Render top scorers table
    function renderTopScorers(scorers) {
        // Limit to top 50
        const top50 = scorers.slice(0, 50);
        const tableRows = top50.map((scorer, index) => {
            const player = scorer.player || {};
            const team = scorer.team || {};
            const playerName = player.name || 'N/A';
            const teamName = team.name || 'N/A';
            const goals = scorer.goals?.overall || 0;
            const penalties = scorer.penalties;
            const position = scorer.pos || (index + 1);
            
            const goalsDisplay = penalties !== null && penalties > 0 
                ? `${goals} <span class="text-gray-400 text-sm font-normal">(${penalties})</span>`
                : goals;
            
            return `
                <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3 text-gray-300">${position}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white">
                                ${(playerName || '').charAt(0)}
                            </div>
                            <span class="text-white font-medium">${playerName}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white">
                                ${(teamName || '').charAt(0)}
                            </div>
                            <span class="text-gray-300">${teamName}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-green-400 font-bold text-lg">${goalsDisplay}</span>
                    </td>
                </tr>
            `;
        }).join('');
        
        topScorersContainer.innerHTML = `
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-green-600 text-white">
                            <th class="px-4 py-3 text-left font-semibold">#</th>
                            <th class="px-4 py-3 text-left font-semibold">Tên cầu thủ</th>
                            <th class="px-4 py-3 text-left font-semibold">Tên đội</th>
                            <th class="px-4 py-3 text-center font-semibold">Bàn thắng</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        `;
    }
});
</script>
@endpush
@endsection

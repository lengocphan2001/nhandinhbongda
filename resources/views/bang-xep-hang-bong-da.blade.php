@extends('layouts.app')

@section('title', 'Bảng Xếp Hạng Bóng Đá - XOILAC TV')

@section('content')
<div class="bg-gray-800 rounded-lg p-6">
    <!-- League Selector with Search -->
    <div class="mb-6">
        <div class="flex gap-4 items-start">
            <!-- Search Input -->
            <div class="relative w-full max-w-md flex-1">
                <input 
                    type="text" 
                    id="leagueSearch" 
                    placeholder="Tìm kiếm giải đấu..." 
                    class="w-full bg-gray-700 text-white px-4 py-2 pr-10 rounded border border-gray-600 hover:border-green-500 focus:outline-none focus:border-green-500 transition-colors"
                    autocomplete="off"
                    value="{{ $selectedLeague ? $selectedLeague['name'] : '' }}"
                >
                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <!-- Dropdown Results -->
                <div id="leagueDropdown" class="hidden absolute z-10 w-full mt-1 bg-gray-700 border border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <!-- Results will be populated here -->
                </div>
            </div>
            
            <!-- Select Dropdown -->
            <div class="relative">
                <select 
                    id="leagueSelect" 
                    class="bg-gray-700 text-white px-4 py-2 pr-8 rounded border border-gray-600 hover:border-green-500 focus:outline-none focus:border-green-500 transition-colors appearance-none cursor-pointer min-w-[250px]"
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
        
        <div id="selectedLeagueInfo" class="mt-2 text-sm text-gray-400" style="display: {{ $selectedLeague ? 'block' : 'none' }};">
            @if($selectedLeague)
            Đang hiển thị: <span class="text-green-400 font-semibold">{{ $selectedLeague['name'] }}</span>
            @if($selectedLeague['country_name'])
                <span class="text-gray-500">({{ $selectedLeague['country_name'] }})</span>
            @endif
            @endif
        </div>
    </div>
    
    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-green-500"></div>
        <p class="mt-4 text-gray-400">Đang tải dữ liệu...</p>
    </div>
    
    <!-- League Table Container -->
    <div id="standingsContainer">
        @if($standings && isset($standings['data']['standings']))
            @include('components.standings-table', ['standings' => $standings])
        @else
            <div class="text-center py-12">
                <p class="text-gray-400 text-lg">Vui lòng chọn giải đấu để xem bảng xếp hạng</p>
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
    const standingsContainer = document.getElementById('standingsContainer');
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
            fetch(`{{ route('api.leagues.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    displayResults(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }, 300);
    });
    
    function displayResults(leagues) {
        if (leagues.length === 0) {
            dropdown.innerHTML = '<div class="p-4 text-gray-400 text-center">Không tìm thấy giải đấu</div>';
            dropdown.classList.remove('hidden');
            return;
        }
        
        dropdown.innerHTML = leagues.slice(0, 10).map(league => `
            <div class="p-3 hover:bg-gray-600 cursor-pointer border-b border-gray-600 last:border-0" 
                 onclick="selectLeague(${league.id}, '${league.name.replace(/'/g, "\\'")}')">
                <div class="text-white font-medium">${league.name}</div>
                <div class="text-gray-400 text-xs">${league.country_name || ''}</div>
            </div>
        `).join('');
        
        dropdown.classList.remove('hidden');
    }
    
    window.selectLeague = function(leagueId, leagueName) {
        leagueSelect.value = leagueId;
        searchInput.value = leagueName;
        dropdown.classList.add('hidden');
        loadStandings(leagueId);
    };
    
    // Select dropdown change
    leagueSelect.addEventListener('change', function() {
        const leagueId = this.value;
        if (leagueId) {
            loadStandings(leagueId);
        } else {
            standingsContainer.innerHTML = '<div class="text-center py-12"><p class="text-gray-400 text-lg">Vui lòng chọn giải đấu để xem bảng xếp hạng</p></div>';
            selectedLeagueInfo.style.display = 'none';
        }
    });
    
    // Load standings via AJAX
    function loadStandings(leagueId) {
        // Show loading
        loadingIndicator.classList.remove('hidden');
        standingsContainer.innerHTML = '';
        selectedLeagueInfo.style.display = 'none';
        
        fetch(`{{ route('api.standings') }}?league_id=${leagueId}`)
            .then(response => response.json())
            .then(data => {
                loadingIndicator.classList.add('hidden');
                
                if (data.error) {
                    standingsContainer.innerHTML = `<div class="text-center py-12"><p class="text-red-400">${data.error}</p></div>`;
                    return;
                }
                
                // Update selected league info
                if (data.league) {
                    const countryName = data.league.country_name ? `(${data.league.country_name})` : '';
                    selectedLeagueInfo.innerHTML = `Đang hiển thị: <span class="text-green-400 font-semibold">${data.league.name}</span> <span class="text-gray-500">${countryName}</span>`;
                    selectedLeagueInfo.style.display = 'block';
                }
                
                // Render standings
                if (data.standings && data.standings.data && data.standings.data.standings) {
                    renderStandings(data.standings.data);
                } else {
                    standingsContainer.innerHTML = '<div class="text-center py-12"><p class="text-gray-400 text-lg">Không có dữ liệu bảng xếp hạng</p></div>';
                }
            })
            .catch(error => {
                loadingIndicator.classList.add('hidden');
                standingsContainer.innerHTML = '<div class="text-center py-12"><p class="text-red-400">Có lỗi xảy ra khi tải dữ liệu</p></div>';
                console.error('Error:', error);
            });
    }
    
    // Render standings table
    function renderStandings(standingsData) {
        const hasGroups = standingsData.has_groups == 1;
        const standings = standingsData.standings;
        
        if (hasGroups && Array.isArray(standings) && standings.length > 0 && Array.isArray(standings[0])) {
            // Cup with groups
            let html = '';
            standings.forEach((group, groupIndex) => {
                if (Array.isArray(group) && group.length > 0 && group[0].group_name) {
                    html += `
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-white mb-4">${group[0].group_name || 'Group ' + String.fromCharCode(65 + groupIndex)}</h3>
                            <div class="overflow-x-auto">
                                ${renderTable(group)}
                            </div>
                        </div>
                    `;
                }
            });
            standingsContainer.innerHTML = html;
        } else {
            // League without groups
            standingsContainer.innerHTML = `<div class="overflow-x-auto">${renderTable(standings)}</div>`;
        }
    }
    
    // Render table
    function renderTable(teams) {
        const tableRows = teams.map((team, index) => {
            const position = team.overall?.position || (index + 1);
            const goalsDiff = team.overall?.goals_diff || 0;
            const goalsDiffClass = goalsDiff > 0 ? 'text-green-400' : goalsDiff < 0 ? 'text-red-400' : '';
            const goalsDiffText = goalsDiff > 0 ? `+${goalsDiff}` : goalsDiff;
            
            // Always use first letter of team name as logo
            const logoHtml = `<div class="w-6 h-6 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white">${(team.team_name || '').charAt(0)}</div>`;
            
            const formHtml = (team.recent_form || '').split('').map(result => {
                const color = result === 'W' ? 'bg-green-500' : result === 'D' ? 'bg-yellow-500' : 'bg-red-500';
                return `<span class="w-5 h-5 ${color} rounded text-xs flex items-center justify-center text-white font-bold">${result}</span>`;
            }).join('');
            
            return `
                <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                    <td class="px-4 py-3 text-gray-300">${position}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            ${logoHtml}
                            <span class="text-white font-medium">${team.team_name || 'N/A'}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-300">${team.overall?.games_played || 0}</td>
                    <td class="px-4 py-3 text-center text-gray-300">${team.overall?.won || 0}</td>
                    <td class="px-4 py-3 text-center text-gray-300">${team.overall?.draw || 0}</td>
                    <td class="px-4 py-3 text-center text-gray-300">${team.overall?.lost || 0}</td>
                    <td class="px-4 py-3 text-center text-gray-300">
                        <span class="${goalsDiffClass}">${goalsDiffText}</span>
                    </td>
                    <td class="px-4 py-3 text-center text-white font-semibold">${team.overall?.points || 0}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1">${formHtml}</div>
                    </td>
                </tr>
            `;
        }).join('');
        
        return `
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-green-600 text-white">
                        <th class="px-4 py-3 text-left font-semibold">#</th>
                        <th class="px-4 py-3 text-left font-semibold">Đội</th>
                        <th class="px-4 py-3 text-center font-semibold">Số trận đấu</th>
                        <th class="px-4 py-3 text-center font-semibold">Thắng</th>
                        <th class="px-4 py-3 text-center font-semibold">Hòa</th>
                        <th class="px-4 py-3 text-center font-semibold">Thua</th>
                        <th class="px-4 py-3 text-center font-semibold">Bàn thắng</th>
                        <th class="px-4 py-3 text-center font-semibold">Điểm</th>
                        <th class="px-4 py-3 text-center font-semibold">Phong độ</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        `;
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Auto-load default league on page load
    @if($selectedLeagueId)
        const defaultLeagueId = {{ $selectedLeagueId }};
        if (defaultLeagueId) {
            leagueSelect.value = defaultLeagueId;
            @if($selectedLeague)
                searchInput.value = '{{ $selectedLeague['name'] }}';
            @endif
            // Load standings if not already loaded via server-side
            @if(!$standings || !isset($standings['data']['standings']))
                loadStandings(defaultLeagueId);
            @endif
        }
    @endif
});
</script>
@endpush
@endsection

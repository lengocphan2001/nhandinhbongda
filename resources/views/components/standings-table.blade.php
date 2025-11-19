@php
    $standingsData = $standings['data']['standings'];
    $hasGroups = isset($standings['data']['has_groups']) && $standings['data']['has_groups'] == 1;
@endphp

@if($hasGroups && is_array($standingsData) && count($standingsData) > 0 && is_array($standingsData[0]))
    <!-- Display grouped standings (for cups) -->
    @foreach($standingsData as $groupIndex => $group)
        @if(is_array($group) && count($group) > 0 && isset($group[0]['group_name']))
        <div class="mb-8">
            <h3 class="text-xl font-bold text-white mb-4">{{ $group[0]['group_name'] ?? 'Group ' . chr(65 + $groupIndex) }}</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-xs sm:text-sm min-w-[700px]">
                    <thead>
                        <tr class="bg-green-600 text-white">
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold">#</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold">Đội</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Số trận đấu</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Thắng</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Hòa</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Thua</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Bàn thắng</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Điểm</th>
                            <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Phong độ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $team)
                        <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-gray-300">{{ $team['overall']['position'] ?? $loop->iteration }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-5 h-5 sm:w-6 sm:h-6 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">
                                        {{ substr($team['team_name'] ?? '', 0, 1) }}
                                    </div>
                                    <span class="text-white font-medium truncate min-w-0">{{ $team['team_name'] ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">{{ $team['overall']['games_played'] ?? 0 }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">{{ $team['overall']['won'] ?? 0 }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">{{ $team['overall']['draw'] ?? 0 }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">{{ $team['overall']['lost'] ?? 0 }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">
                                @php
                                    $goalsDiff = $team['overall']['goals_diff'] ?? 0;
                                @endphp
                                @if($goalsDiff > 0)
                                    <span class="text-green-400">+{{ $goalsDiff }}</span>
                                @elseif($goalsDiff < 0)
                                    <span class="text-red-400">{{ $goalsDiff }}</span>
                                @else
                                    <span>{{ $goalsDiff }}</span>
                                @endif
                            </td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-white font-semibold">{{ $team['overall']['points'] ?? 0 }}</td>
                            <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                                <div class="flex items-center justify-center gap-0.5 sm:gap-1 flex-wrap">
                                    @if(isset($team['recent_form']) && !empty($team['recent_form']))
                                        @foreach(str_split($team['recent_form']) as $result)
                                            @if($result === 'W')
                                                <span class="w-4 h-4 sm:w-5 sm:h-5 bg-green-500 rounded text-xs flex items-center justify-center text-white font-bold">W</span>
                                            @elseif($result === 'D')
                                                <span class="w-4 h-4 sm:w-5 sm:h-5 bg-yellow-500 rounded text-xs flex items-center justify-center text-white font-bold">D</span>
                                            @else
                                                <span class="w-4 h-4 sm:w-5 sm:h-5 bg-red-500 rounded text-xs flex items-center justify-center text-white font-bold">L</span>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    @endforeach
@else
    <!-- Display single table (for leagues) -->
    <div class="overflow-x-auto">
        <table class="w-full text-xs sm:text-sm min-w-[700px]">
            <thead>
                <tr class="bg-green-600 text-white">
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold">#</th>
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-left font-semibold">Đội</th>
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Số trận đấu</th>
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Thắng</th>
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Hòa</th>
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Thua</th>
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Bàn thắng</th>
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Điểm</th>
                    <th class="px-2 sm:px-4 py-2 sm:py-3 text-center font-semibold">Phong độ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($standingsData as $team)
                <tr class="border-b border-gray-700 hover:bg-gray-700 transition-colors">
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-gray-300">{{ $team['overall']['position'] ?? $loop->iteration }}</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 min-w-0">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-5 h-5 sm:w-6 sm:h-6 bg-gray-600 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0">
                                {{ substr($team['team_name'] ?? '', 0, 1) }}
                            </div>
                            <span class="text-white font-medium truncate min-w-0">{{ $team['team_name'] ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">{{ $team['overall']['games_played'] ?? 0 }}</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">{{ $team['overall']['won'] ?? 0 }}</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">{{ $team['overall']['draw'] ?? 0 }}</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">{{ $team['overall']['lost'] ?? 0 }}</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-gray-300">
                        @php
                            $goalsDiff = $team['overall']['goals_diff'] ?? 0;
                        @endphp
                        @if($goalsDiff > 0)
                            <span class="text-green-400">+{{ $goalsDiff }}</span>
                        @elseif($goalsDiff < 0)
                            <span class="text-red-400">{{ $goalsDiff }}</span>
                        @else
                            <span>{{ $goalsDiff }}</span>
                        @endif
                    </td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center text-white font-semibold">{{ $team['overall']['points'] ?? 0 }}</td>
                    <td class="px-2 sm:px-4 py-2 sm:py-3 text-center">
                        <div class="flex items-center justify-center gap-0.5 sm:gap-1 flex-wrap">
                            @if(isset($team['recent_form']) && !empty($team['recent_form']))
                                @foreach(str_split($team['recent_form']) as $result)
                                    @if($result === 'W')
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 bg-green-500 rounded text-xs flex items-center justify-center text-white font-bold">W</span>
                                    @elseif($result === 'D')
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 bg-yellow-500 rounded text-xs flex items-center justify-center text-white font-bold">D</span>
                                    @else
                                        <span class="w-4 h-4 sm:w-5 sm:h-5 bg-red-500 rounded text-xs flex items-center justify-center text-white font-bold">L</span>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif


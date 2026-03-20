@if($rounds_count > 0)
    <div class="-mx-3 flex justify-center d-none" id="archiveTable">
        <div class="max-w-5xl md:max-w-7xl">
            <table class="table roundsTable">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Spiele</th>
                        <th>Online</th>
                        {{-- <th>Listen</th>--}}
                        <th>Teilnehmende Personen</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <p class="mt-4">
                {{ $rounds_count }} Runden
            </p>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const table = document.querySelector('.roundsTable');
                const wrapper = document.getElementById('archiveTable');

                if (!table || !wrapper) {
                    return;
                }

                const tbody = table.querySelector('tbody');
                const ajaxUrl = '/rounds/ajax/{{ $selectedGroup->id }}/{{ isset($player) ? $player->id : '' }}';
                const currentPlayerId = {{ auth()->user()->player->id }};
                const shouldHighlightPlayer = {{ isset($player) && ($player->id != auth()->user()->player->id) || !isset($player) ? 'true' : 'false' }};

                fetch(ajaxUrl)
                    .then((response) => response.json())
                    .then((payload) => {
                        const rows = Array.isArray(payload.data) ? payload.data : [];

                        rows.forEach((entry) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${entry.date ?? ''}</td>
                                <td>${entry.games_count ?? ''}</td>
                                <td>${entry.liveRound ?? ''}</td>
                                <td>${entry.players ?? ''}</td>
                            `;

                            if (shouldHighlightPlayer) {
                                let playerIds = [];
                                try {
                                    playerIds = JSON.parse(entry.playerIDs || '[]');
                                } catch (e) {
                                    playerIds = [];
                                }

                                if (playerIds.includes(currentPlayerId)) {
                                    row.querySelectorAll('td').forEach((cell) => cell.classList.add('bg-primary-light'));
                                }
                            }

                            tbody.appendChild(row);
                        });

                        wrapper.classList.remove('d-none');
                    })
                    .catch(() => {
                        wrapper.classList.remove('d-none');
                    });
            });
        </script>
    @endpush

@else
    <h5>Bisher wurden keine Runden gespielt.</h5>
@endif

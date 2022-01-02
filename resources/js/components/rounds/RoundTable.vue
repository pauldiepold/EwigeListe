<template>
    <div>
        <div class="row justify-content-center mt-4">
            <div class="col col-xl-6 col-lg-8 col-md-10 col-sm-12">
                <div class="table-responsive">
                    <table class="table mb-1">
                        <thead>
                            <tr class="border-bottom-thick">
                                <!-- Spieler Kopfzeile -->
                                <th v-for="player in round.players" :key="player.id"
                                    class="tw-flex-col tw-items-center">
                                    <a :href="player.path">
                                        <img :src="player.avatar_path"
                                             class="tw-mx-auto tw-my-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full"
                                             :class="{ 'tw-shadow-green' : round.online_players.map(o => o['id']).includes(player.id)}">
                                    </a>

                                    <a :class="{ 'text-dark': round.dealer_index !== player.index,
                                                 'active-player': round.players.length > 5 && round.active_players.map(o => o['id']).includes(player.id) }"
                                       :href="player.path">
                                        {{ player.surname }}
                                    </a>
                                </th>

                                <!-- Punkte -->
                                <th class="text-dark">
                                    <img src="/storage/avatars/default.jpg"
                                         class="tw-mx-auto tw-mb-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full tw-opacity-0">
                                    Punkte
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(game, index) in round.games" :key="game.id"
                                :class="{ 'bg-danger-light': game.misplay,
                                          'bg-light': game.solo,
                                          'border-bottom-thick': game.dealer_index + 1 === round.players.length && !game.solo && !game.misplay }">
                                <!-- Spieler Punktzahl -->
                                <td v-for="(player, index) in points[index]"
                                    :class="{ 'font-weight-bold': player.won }">
                                    {{ player.points }}
                                </td>

                                <!-- Punkte -->
                                <td>{{ game.points }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['round'],
    data() {
        return {}
    },
    computed: {
        points() {
            let i;
            let k;
            let playerPoints = [];
            let player;
            for (k = 0; k < this.round.players.length; k++) {
                playerPoints[k] = 0;
            }
            let output = [];
            for (i = 0; i < this.round.games.length; i++) {
                output[i] = [];
                for (k = 0; k < this.round.players.length; k++) {
                    if (this.round.games[i].players.map(o => o['id']).includes(this.round.players[k].id)) {
                        player = this.round.games[i].players.find(player => player.id === this.round.players[k].id);
                        playerPoints[k] = playerPoints[k] + player.points;
                        output[i][k] = {points: playerPoints[k], won: player.won};
                    } else {
                        output[i][k] = {points: '-', won: false};
                    }
                }
            }
            return output;
        }
    },
    methods: {}
};
</script>

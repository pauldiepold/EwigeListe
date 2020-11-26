<template>
    <div>
        <tabs @clicked="fullscreenOnIfMobile">
            <tab name="Runde" icon="fa-play-circle" :selected="true">
                <create-game v-if="round.live_round === null" :round="round" @updated="fetchData"/>
                <round-table :round="round"/>
                <delete-game v-if="round.live_round === null" :round="round" @updated="fetchData(); deleteLastGame();"/>
                <round-info :round="round"/>
            </tab>

            <tab v-if="round.live_round !== null" name="Live" icon="fa-dice" :selected="false" @clicked="alert('test')">
                <div id="fullscreen" class="tw-w-100 tw-relative tw-pt-50p">
                    <div
                        class="tw-absolute tw-bottom-0 tw-top-0 tw-left-0 tw-right-0 tw-overflow-hidden"
                        :class="{'tw-rounded-xl tw-shadow-xl': !fullscreen}"
                        style="background-image: url('/img/wood.jpg');">
                        <div class="tw-relative tw-h-full tw-w-full">

                            <!-- Fullscreen ausschalten -->
                            <div class="tw-absolute tw-ml-2 tw-mt-2 tw-top-0 tw-left-0">
                                <i v-if="allPlayersOnline && mobile && fullscreen"
                                   class="fas fa-compress tw-text-2xl tw-text-gray-600"
                                   @click="fullscreenOff"></i>
                            </div>

                            <!-- Fullscreen? -->
                            <div v-if="allPlayersOnline && mobile && !fullscreen"
                                 class="center-absolute live-overlay tw-p-2 tw-w-48">
                                <p> Bitte aktiviere den Fullscreen-Modus! </p>
                                <i class="fas fa-expand tw-text-4xl" @click="fullscreenOn"></i>
                            </div>

                            <!-- Landscape Modus? -->
                            <div v-if="allPlayersOnline && mobile && !landscape && fullscreen"
                                 class="center-absolute live-overlay tw-w-48">
                                <p>Bitte drehe dein Gerät in den Landscape Modus!</p>
                                <i class="fas fa-sync tw-text-4xl"></i>
                            </div>

                            <!-- Spieler Online? -->
                            <div v-if="!allPlayersOnline" class="center-absolute live-overlay tw-p-4">
                                <p>Bitte warte, bis alle Spieler online sind:</p>
                                <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                                    <div v-for="player in round.active_players">
                                        <img :src="player.avatar_path"
                                             class="tw-mx-auto tw-my-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full"
                                             :class="{ 'tw-shadow-green' : pluck(round.online_players, 'id').includes(player.id)}">
                                        {{ player.surname }}
                                    </div>
                                </div>
                            </div>

                            <!-- Spieler bereit? -->
                            <div
                                v-if="allPlayersOnline && !round.current_live_game && (!mobile || (landscape && fullscreen))"
                                class="center-absolute live-overlay tw-p-4 tw-flex tw-content-center tw-justify-around tw-w-1/3"
                                :class="{'tw-w-3/4': round.last_live_game}">

                                <!-- Wertung anzeigen -->
                                <div class="tw-mr-6 tw-flex tw-flex-col tw-justify-center"
                                     v-if="round.last_live_game">
                                    <div class="tw-font-bold">{{ round.last_live_game.spieltyp }}</div>
                                    <div class="tw-flex tw-justify-around tw-my-2">
                                        <div>Re: {{ round.last_live_game.augen[0] }}</div>
                                        <div>Kontra: {{ round.last_live_game.augen[1] }}</div>
                                    </div>
                                    <table class="tw-w-full">
                                        <tr v-for="(item, index) in round.last_live_game.wertung"
                                            class="tw-border-0"
                                            :class="{'tw-border-b-2': index === round.last_live_game.wertung.length - 1}">
                                            <td class="tw-text-left">{{ item[0] }}</td>
                                            <td>{{ item[1] }}</td>
                                        </tr>
                                        <tr class="tw-border-0">
                                            <td></td>
                                            <td>
                                                {{ round.last_live_game.wertungsPunkte > 0 ? '+' : '' }}{{
                                                    round.last_live_game.wertungsPunkte < 0 ? '-' : ''
                                                }}{{
                                                    round.last_live_game.wertungsPunkte
                                                }}
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="tw-flex tw-justify-around tw-my-2">
                                        <div v-for="player in round.active_players"
                                             v-if="round.last_live_game.winners.includes(player.id)">
                                            <img :src="player.avatar_path"
                                                 class="tw-mx-auto tw-my-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full"
                                                 alt="">
                                            {{ player.surname }}
                                        </div>
                                    </div>
                                    <div class=" tw-mb-2">
                                        <b>{{ round.last_live_game.gewinntRe ? 'Re' : 'Kontra' }}</b> gewinnt mit
                                        {{ round.last_live_game.wertungsPunkte }} Punkten!
                                    </div>
                                </div>

                                <!-- Ready Check -->
                                <div v-if="!allPlayersReady" class="tw-flex tw-flex-col tw-justify-center">
                                    <div v-if="aktiv && !round.ready_players.includes(round.auth_id)" class="tw-mb-4">
                                        <button class="btn btn-primary" @click="whisperReady">
                                            Bereit?
                                        </button>
                                    </div>
                                    <div v-if="aktiv" class="tw-mb-2">
                                        <p>
                                            Bitte warte, bis alle Spieler bereit sind:
                                        </p>
                                    </div>
                                    <div v-if="aktiv" class="tw-grid tw-grid-cols-2 tw-gap-2">
                                        <div v-for="player in round.active_players">
                                            <img :src="player.avatar_path"
                                                 class="tw-mx-auto tw-my-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full"
                                                 :class="{ 'tw-shadow-green' : round.ready_players.includes(player.id)}">
                                            {{ player.surname }}
                                        </div>
                                    </div>
                                    <div v-if="!aktiv || (aktiv && allPlayersReady)">
                                        Neue Runde wird gestartet...
                                    </div>
                                </div>
                            </div>

                            <div v-show="allPlayersOnline && (!mobile || (landscape && fullscreen))">
                                <live-game v-if="round.current_live_game"
                                           ref="live_game"
                                           :round="round"
                                           :mobile="mobile"
                                           :aktiv="aktiv"
                                           @neues-spiel-starten="neuesSpielStarten"
                                           @message="whisperMessage"
                                           @reload-live-game="reloadLiveGame"/>
                            </div>
                        </div>
                    </div>
                </div>
            </tab>

            <tab v-if="round.games.length >= 4" name="Statistiken" icon="fa-chart-area" :selected="false">
                <template v-slot:default="props">
                    <round-graph :round_id="round.id" :key="props.tabKey"></round-graph>
                </template>
            </tab>

            <tab name="Listen" icon="fa-list-alt" :selected="false">
                <template v-slot:default="props">
                    <update-groups :round-input="round"
                                   :can-update="canUpdate"
                                   :key="props.tabKey"/>
                </template>
            </tab>
        </tabs>
    </div>
</template>

<script>

export default {
    components: {},
    props: {
        roundProp: Object,
        canUpdate: Boolean,
    },
    data() {
        return {
            round: this.roundProp,
            landscape: false,
            fullscreen: false,
            mobile: false,
        }
    },
    mounted() {
    },
    created() {
        this.orderActivePlayers();
        this.getOrientation();
        this.getFullscreen();
        window.addEventListener("resize", this.getOrientation);
        window.addEventListener("fullscreenchange", this.getFullscreen);
        this.presenceChannel
            .here(players => {
                this.round.online_players = players;
                this.getWatchingPlayers();
            })
            .joining(player => {
                this.round.online_players.push(player);
                this.getWatchingPlayers();
            })
            .leaving(player => {
                this.round.online_players.splice(this.round.online_players.indexOf(player), 1);
                this.round.ready_players = [];
                this.getWatchingPlayers();
            })
            .listen('RoundUpdated', e => {
                this.reloadLiveGame();
            })
            .listenForWhisper('ready', e => {
                this.round.ready_players.push(e.id);
                this.readyCheck();
            })
            .listenForWhisper('message', e => {
                //this.$refs.live_game.pushMessage(e.message);
            });
    },
    destroyed() {
        window.removeEventListener("resize", this.getOrientation);
        window.removeEventListener("fullscreenchange", this.getFullscreen);
    },
    computed: {
        aktiv() {
            return this.pluck(this.round.active_players, 'id').includes(this.round.auth_id);
        },
        presenceChannel() {
            return window.Echo
                .join('round.' + this.round.id);
        },
        allPlayersOnline() {
            return this.pluck(this.round.active_players, 'id')
                .every(id => this.pluck(this.round.online_players, 'id').includes(id));
        },
        allPlayersReady() {
            return this.pluck(this.round.active_players, 'id')
                .every(id => this.round.ready_players.includes(id));
        },
        not_ready_players() {
            return this.pluck(this.round.active_players, 'id')
                .filter(id => !this.round.ready_players.includes(id));
        },
    },
    methods: {
        getWatchingPlayers() {
            this.round.watching_players = this.round.online_players.filter(player => {
                return !this.pluck(this.round.active_players, 'id').includes(player.id);
            });
        },
        orderActivePlayers() {
            /* Aktive Spieler sortieren: Immer ausgehend von der eigenen Sitzposition */
            /* Nur wenn man selbst aktiv ist */
            if (this.pluck(this.round.players, 'id').includes(this.round.auth_id)) {
                let output = [];
                let i;
                let counter;
                let startIndex = this.round.players.find(player => player.id === this.round.auth_id).index;

                /* Wenn man selbst aussetzt, und bei 7er Runden möglicherweise auch der nächste, Index erhöhen */
                while (!this.pluck(this.round.active_players, 'index').includes(startIndex)) {
                    if (startIndex >= this.round.players.length - 1) {
                        startIndex = 0;
                    } else {
                        startIndex++;
                    }
                }
                counter = this.pluck(this.round.active_players, 'index').indexOf(startIndex);

                for (i = 0; i <= 3; i++) {
                    output.push(this.round.active_players[counter]);
                    if (counter >= 3) {
                        counter = 0;
                    } else {
                        counter++;
                    }
                }

                this.round.active_players = output;
            }
        },
        readyCheck() {
            if ((this.round.auth_id === this.round.first_player.id) && this.allPlayersReady) {
                this.neuesSpielStarten();
            }
        },
        whisperReady() {
            this.round.ready_players.push(this.round.auth_id);
            this.readyCheck();
            this.presenceChannel
                .whisper('ready', {
                    'id': this.round.auth_id,
                });
        },
        whisperMessage(message) {
            this.presenceChannel
                .whisper('message', {
                    'message': message
                });
        },

        reconnectChannels() {
            this.round.online_players = Object.values(this.presenceChannel.subscription.members.members);
            this.getWatchingPlayers();
        },

        fetchData() {
            return new Promise((resolve, reject) => {
                axios.get('/api/rounds/' + this.roundProp.id + '/fetchData')
                    .then(response => {
                        this.round = response.data.data;
                        this.reconnectChannels();
                        resolve(response);
                    });
            });
        },

        reloadLiveGame() {
            this.fetchData()
                .then((response) => {
                    this.orderActivePlayers();
                    if (this.round.current_live_game) {
                        this.$refs.live_game.copyDataFromProp();
                    }
                });
        },

        neuesSpielStarten() {
            axios.post('/api/live/' + this.round.live_round.id + '/spielStarten');
        },

        fullscreenOn() {
            let elem = document.getElementById('fullscreen');
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        },
        fullscreenOff() {
            document.exitFullscreen();
        },
        fullscreenOnIfMobile() {
            if (this.mobile && !this.fullscreen) {
                this.fullscreenOn();
            }
        },
        getFullscreen() {
            let fullscreen = !!document.webkitIsFullScreen;
            this.fullscreen = fullscreen;
            return fullscreen;
        },
        getOrientation() {
            this.landscape = window.innerWidth > window.innerHeight;
            this.mobile = window.screen.width <= 900;
            if (!this.mobile && this.fullscreen) {
                this.fullscreenOff();
            }
        },

        deleteLastGame() {
            this.round.games.splice(this.round.games.indexOf(this.round.games.length - 1), 1);
        },
    },
};
</script>

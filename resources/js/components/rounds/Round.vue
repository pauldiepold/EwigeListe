<template>
    <div>
        <tabs @clicked="fullscreenOnIfMobile">
            <tab name="Runde" icon="fa-play-circle" :selected="true">
                <create-game :round="round" @updated="fetchData"/>
                <round-table :round="round"/>
                <delete-game :round="round" @updated="fetchData(); deleteLastGame();"/>
                <round-info :round="round"/>
            </tab>

            <tab v-if="round.live_round !== null" name="Live" icon="fa-dice" :selected="false" @clicked="alert('test')">
                <div id="fullscreen" class="tw-w-100 tw-relative tw-pt-50p">
                    <div
                        class="tw-absolute tw-bottom-0 tw-top-0 tw-left-0 tw-right-0"
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
                                             :class="{ 'tw-shadow-green' : round.online_players.includes(player.id)}">
                                        {{ player.surname }}
                                    </div>
                                </div>
                            </div>

                            <!-- Spieler bereit? -->
                            <div
                                v-if="allPlayersOnline && !round.current_live_game && (!mobile || (landscape && fullscreen))"
                                class="center-absolute live-overlay tw-p-4">
                                <div v-if="!round.ready_players.includes(round.auth_id)" class="tw-mb-4">
                                    <button class="btn btn-primary" @click="whisperReady">
                                        Bereit?
                                    </button>
                                </div>
                                <div v-if="round.created_by.id === round.auth_id" class="">
                                    <button class="btn btn-primary tw-mb-4" :disabled="false && !allPlayersReady"
                                            @click="neuesSpielStarten">
                                        Neues Spiel starten
                                    </button>
                                    <p v-if="!allPlayersReady">
                                        Bitte warte, bis alle Spieler bereit sind:
                                    </p>
                                    <p v-else>
                                        Alle Spieler sind bereit!
                                    </p>
                                </div>
                                <div v-if="round.created_by.id !== round.auth_id" class="tw-mb-4">
                                    <p v-if="!allPlayersReady">
                                        Bitte warte, bis alle Spieler bereit sind:
                                    </p>
                                    <p v-else>
                                        Bitte warte, bis {{ round.created_by.surname }} ein neues Spiel gestartet hat.
                                    </p>
                                </div>
                                <div class="tw-grid tw-grid-cols-2 tw-gap-2">
                                    <div v-for="player in round.active_players">
                                        <img :src="player.avatar_path"
                                             class="tw-mx-auto tw-my-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full"
                                             :class="{ 'tw-shadow-green' : round.ready_players.includes(player.id)}">
                                        {{ player.surname }}
                                    </div>
                                </div>
                            </div>

                            <div v-show="allPlayersOnline && (!mobile || (landscape && fullscreen))">
                                <div class="tw-absolute tw-bottom-0 tw-left-0">
                                    <i class="far fa-plus-square tw-text-3xl tw-text-gray-500 tw-ml-4 tw-mb-4 tw-cursor-pointer"
                                       @click="neuesSpielStarten"></i>
                                </div>
                                <live-game ref="live_game" v-if="round.current_live_game" :round="round"/>
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
        this.getOrientation();
        this.getFullscreen();
        window.addEventListener("resize", this.getOrientation);
        window.addEventListener("fullscreenchange", this.getFullscreen);
        this.presenceChannel
            .here(players => {
                this.round.online_players = this.pluck(players, 'id');
            })
            .joining(player => {
                this.round.online_players.push(player.id);
            })
            .leaving(player => {
                this.round.online_players.splice(this.round.online_players.indexOf(player.id), 1);
                this.round.ready_players = [];
            })
            .listen('RoundUpdated', e => {
                this.fetchData()
                    .then((response) => {
                        this.$refs.live_game.copyDataFromProp();
                    });
            })
            .listenForWhisper('ready', e => {
                this.round.ready_players.push(e.id);
            });
    },
    destroyed() {
        window.removeEventListener("resize", this.getOrientation);
        window.removeEventListener("fullscreenchange", this.getFullscreen);
    },
    computed: {
        presenceChannel() {
            return window.Echo
                .join('round.' + this.round.id);
        },
        allPlayersOnline() {
            return this.pluck(this.round.active_players, 'id')
                .every(id => this.round.online_players.includes(id));
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
        whisperReady() {
            this.round.ready_players.push(this.round.auth_id);
            this.presenceChannel
                .whisper('ready', {
                    'id': this.round.auth_id,
                });
        },

        reconnectChannels() {
            this.round.online_players = this.pluck(Object.values(this.presenceChannel.subscription.members.members), 'id');
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

        pluck(array, key) {
            return array.map(o => o[key]);
        },
    },
};
</script>

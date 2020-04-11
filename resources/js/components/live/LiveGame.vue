<template>
    <div class="playingCards">
        <div class="tw-flex tw-justify-around tw-items-center">
            <span v-if="liveGame !== 'null'">LiveGameID: {{ liveGame.id }}</span>
            <span v-if="liveGame !== 'null'">Dran: {{ liveGame.dran }}</span>
            <button class="btn btn-primary"
                    v-if="true"
                    @click="spielStarten">
                Neues Spiel starten
            </button>
        </div>
        <p v-if="error !== ''" v-text="error" class="tw-font-bold tw-text-red-700 tw-my-4"></p>
        <hr>

        <div v-if="aktiv">
            <div v-if="liveGame.phase === 0">
                <button class="btn btn-primary"
                        @click="kartenGeben">
                    Karten austeilen
                </button>
            </div>

            <div v-if="liveGame.phase === 1">
                <div v-if="liveGame.dran === authId">
                    <p class="tw-font-bold">Bist du gesund?</p>
                    <div class="tw-flex tw-justify-around tw-max-w-xs tw-mx-auto">
                        <button class="btn btn-primary" @click="gesund(true)">Ja!</button>
                        <button class="btn btn-primary" @click="gesund(false)">Nein!</button>
                    </div>
                </div>
                <div v-else>
                    <p class="tw-font-bold">Bitte warte, bis alle Spieler ihren Vorbehalt gemeldet haben.</p>
                </div>
            </div>

            <div v-if="liveGame.phase === 2">
                <div v-if="liveGame.dran === authId" class="tw-flex tw-flex-col tw-max-w-4xs tw-mx-auto">
                    <button class="btn btn-outline-primary tw-my-1"
                            v-for="vorbehalt in ich.moeglicheVorbehalte"
                            @click="vorbehaltSenden(vorbehalt)"
                            v-text="vorbehalt"/>
                </div>
                <div v-else>
                    <p class="tw-font-bold">Bitte warte, bis alle Spieler ihren Vorbehalt offengelegt haben.</p>
                </div>
            </div>

            <div v-if="liveGame.phase === 4">
                <stich v-if="liveGame.phase > 0"
                       :stich="liveGame.aktuellerStich.karten"
                       :auth-id="authId"
                       :spieler-ids="liveGame.spielerIDs"/>
            </div>

            <div v-if="liveGame.phase > 0">
                <hr>
                <hand class="tw-mt-7"
                      v-if="ich.hand !== ''"
                      :karten="ich.hand"
                      @karteSpielen="karteSpielen"/>
            </div>

            <div v-if="liveGame.phase === 4">
                <stich v-if="liveGame.phase > 0"
                       :stich="liveGame.letzterStich.karten"
                       :auth-id="authId"
                       :spieler-ids="liveGame.spielerIDs"/>
            </div>
        </div>

        <div class="tw-flex-1" v-if="!aktiv">
            Du setzt dieses Spiel aus!
        </div>


        <hr>
        <p>
            Gerade online:
        </p>
        <ul>
            <li v-for="player in players" v-text="player.name + ' (' + player.id + ')'"></li>
        </ul>
    </div>
</template>

<script>

    import Hand from "./Hand";
    import Stich from "./Stich";

    export default {
        components: {
            Hand,
            Stich
        },

        props: {
            authId: Number,
            roundPlayersIds: Array,
            liveRoundId: Number,
            liveGameInit: {
                required: false,
                default: 'null'
            },
            ichInit: {
                required: false,
                default: 'null'
            }
        },

        data() {
            return {
                players: [],
                liveGame: 'null',
                ich: 'null',
                error: '',
            }
        },

        created() {
            this.liveGame = this.liveGameInit;
            this.ich = this.ichInit;
            this.ich.hand = Object.values(this.ich.hand);
            this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);

            this.presenceChannel
                .here(userIDs => {
                    this.players = userIDs;
                })
                .joining(userID => {
                    this.players.push(userID);
                })
                .leaving(userID => {
                    this.players.splice(this.players.indexOf(userID), 1);
                });

            if (this.aktiv) {
                this.privateChannel
                    .listen('LiveGameDataBroadcasted', e => {
                        this.ich = e.ich;
                        this.ich.hand = Object.values(this.ich.hand);
                        this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);
                        this.liveGame = e.liveGame;
                        this.error = '';
                    });
            } else {
                this.privateChannel
                    .listen('LiveGameDataBroadcastedInaktiv', e => {
                        this.liveGame = e.liveGame;
                    });
            }
        },

        computed: {
            presenceChannel() {
                return window.Echo
                    .join('liveRound.' + this.liveRoundId);
            },

            privateChannel() {
                return window.Echo
                    .private('liveRound.' + this.liveRoundId + '.' + this.authId);
            },

            aktiv() {
                return !this.liveGame.spielerIDsInaktiv.includes(this.authId);
            },

            allPlayersOnline() {
                return this.roundPlayersIds
                    .every(e => this.pluck(this.players, 'id').includes(e));
            }
        },

        methods: {
            pluck(array, key) {
                return array.map(o => o[key]);
            },

            spielStarten() {
                axios.post('/api/live/' + this.liveRoundId + '/spielStarten', {});
            },

            kartenGeben() {
                axios.post('/api/live/' + this.liveGame.id + '/kartenGeben', {});
            },

            gesund(input) {
                axios.post('/api/live/' + this.liveGame.id + '/gesund', {
                    gesund: input
                });
            },

            vorbehaltSenden(input) {
                axios.post('/api/live/' + this.liveGame.id + '/vorbehalt', {
                    vorbehalt: input
                })
                    .then(response => this.error = '')
                    .catch(error => this.handleError(error));
            },

            karteSpielen(karte) {
                this.ich.hand.splice(this.ich.hand.indexOf(karte), 1);
                let karteKopie = karte;
                karteKopie.gespieltVon = this.authId;
                karteKopie.spielbar = false;
                this.liveGame.aktuellerStich.karten.push(karteKopie);

                axios.post('/api/live/' + this.liveGame.id + '/karteSpielen', {
                    karte: karte
                })
                    .then(response => this.error = '')
                    .catch(error => this.handleError(error));
            },

            reloadData() {
                axios.get('/api/live/' + this.liveGame.id + '/reloadData')
                    .then(response => {
                        this.ich = response.data.ich;
                        this.liveGame = response.data.liveGame;
                    });
            },

            reloadPage() {
                window.location.reload(true);
            },

            handleError(error) {
                if (error.response.status === 419) {
                    this.error = error.response.data.message;
                    this.reloadPage();
                }
                if (error.response.status === 422) {
                    this.reloadData();
                    this.error = error.response.data.message;
                }
            }
        }
    };
</script>

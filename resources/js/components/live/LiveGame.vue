<template>
    <div>
        <p v-if="liveGame !== 'null'">LiveGameID: {{ liveGame.id }}</p>
        <p v-if="liveGame !== 'null'">Dran: {{ liveGame.dran }}</p>
        <button class="btn btn-primary"
                v-if="true"
                @click="spielStarten">
            Neues Spiel starten
        </button>
        <button class="btn btn-primary"
                v-if="liveGame.phase === 0"
                @click="kartenGeben">
            Karten austeilen
        </button>
        <hr>
        <div class="tw-flex">
            <div class="tw-flex-1" v-if="aktiv">
                Hand:
                <ul>
                    <li v-if="liveGame !== 'null'"
                        v-for="karte in ich.hand"
                        v-text="karte.farbName + ' ' + karte.wertName"
                        @click="karteSpielen(karte)"/>
                </ul>
            </div>
            <div class="tw-flex-1" v-if="!aktiv">
                Du setzt dieses Spiel aus!
            </div>
            <div class="tw-flex-1">
                <ul>
                    Aktueller Stich:
                    <li v-if="liveGame.phase > 0 && liveGame.aktuellerStich.karten.length > 0"
                        v-for="karte in liveGame.aktuellerStich.karten"
                        v-text="karte.farbName + ' ' + karte.wertName"/>
                </ul>
            </div>
            <div class="tw-flex-1">
                <ul>
                    Letzter Stich:
                    <li v-if="liveGame.phase > 0 && liveGame.letzterStich.karten.length > 0"
                        v-for="karte in liveGame.letzterStich.karten"
                        v-text="karte.farbName + ' ' + karte.wertName"/>
                </ul>
            </div>
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
    export default {
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
            }
        },

        created() {
            this.liveGame = this.liveGameInit;
            this.ich = this.ichInit;

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
                        this.liveGame = e.liveGame;
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

            karteSpielen(karte) {
                axios.post('/api/live/' + this.liveGame.id + '/karteSpielen', {
                    karte: karte
                });
            }
        }
    };
</script>

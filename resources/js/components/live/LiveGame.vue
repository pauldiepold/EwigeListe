<template>
    <div>
        <p v-if="liveGame !== 'null'">LiveGameID: {{ liveGame.id }}</p>
        <button class="btn btn-primary"
                v-if="true"
                @click="spielStarten">
            Karten austeilen
        </button>
        <hr>
        <div class="tw-flex">
            <div class="tw-flex-1">
                Hand:
                <ul>
                    <li v-if="liveGame !== 'null'"
                        v-for="karte in ich.hand"
                        v-text="karte.farbName + ' ' + karte.wertName"
                        @click="karteSpielen(karte)"/>
                </ul>
            </div>
            <div class="tw-flex-1">
                <ul>
                    Aktueller Stich:
                    <li v-if="liveGame !== 'null' && liveGame.aktuellerStich.length > 0"
                        v-for="karte in liveGame.aktuellerStich"
                        v-text="karte.farbName + ' ' + karte.wertName"/>
                </ul>
            </div>
            <div class="tw-flex-1">
                <ul>
                    Letzter Stich:
                    <li v-if="liveGame !== 'null' && liveGame.letzterStich.length > 0"
                        v-for="karte in liveGame.letzterStich"
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
            //this.ich = this.liveGame.spieler.spieler0;

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

            this.privateChannel
                .listen('LiveGameDataBroadcasted', e => {
                    console.log(e);
                    this.ich = e.ich;
                    this.liveGame = e.liveGame;
                    //this.ich = e.liveGame.spieler.spieler0;
                });
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
                axios.post('/api/live/' + this.liveRoundId + '/starteNeuesSpiel', {});
            },

            karteSpielen(karte) {
                axios.post('/api/live/' + this.liveGame.id + '/karteSpielen', {
                    karte: karte
                });
            }
        }
    };
</script>

<template>
    <div class="playingCards">
        <p v-if="error !== ''" v-text="error" class="tw-font-bold tw-text-red-700 tw-my-4"></p>

        <div v-if="!alleSpielerOnline">
            <h4>Bitte warte, bis alle Spieler am Tisch sitzen</h4>
        </div>
        <div v-else>
            <h4 v-if="istPhase(0)">Spielvorbereitung</h4>
            <h4 v-if="istPhase(1) || istPhase(3)">Spielfindung</h4>
            <h4 v-if="istPhase(4)">Spiel -- {{ liveGame.spieltyp }}</h4>
        </div>
        <hr>

        <div class="tw-grid tw-grid-cols-livegame tw-grid-rows-livegame lg:tw-grid-rows-livegame_lg">

            <div class="tw-col-start-1 tw-row-start-2 tw-col-span-3 lg:tw-col-span-1 tw-flex tw-flex-col tw-justify-around tw-items-center tw-p-4">

                <div v-if="istPhase(0)">
                    <button class="btn btn-primary"
                            @click="kartenGeben">
                        Karten austeilen
                    </button>
                </div>


                <!-- ******** Vorbehalte ********* -->
                <div v-if="istPhase(2) && ich.vorbehalt != null" class="tw-font-bold">
                    Bitte warte, bis alle Spieler ihren Vorbehalt bestimmt haben.
                </div>

                <div v-if="istPhase(2) && ich.vorbehalt == null"
                     class="tw-flex tw-flex-col tw-justify-between tw-items-center">

                    <p v-if="binIchDran" class="tw-font-bold">Wähle deinen Vorbehalt:</p>
                    <p v-else class="tw-font-bold">Mögliche Vorbehalte:</p>

                    <div class="tw-flex tw-items-center">
                        <button class="btn btn-primary tw-mr-6 tw-max-w-4xs"
                                :disabled="!binIchDran"
                                v-if="!ich.moeglicheVorbehalte.includes('Hochzeit')"
                                @click="vorbehaltSenden('Gesund')">
                            Gesund
                        </button>
                        <div class="tw-flex tw-flex-col">
                            <button class="btn btn-outline-primary tw-my-1 tw-mr-6"
                                    v-for="vorbehalt in ich.moeglicheVorbehalte"
                                    @click="vorbehaltSenden(vorbehalt)"
                                    v-text="vorbehalt"
                                    v-if="!soli.includes(vorbehalt) && !farbsoli.includes(vorbehalt)"
                                    :disabled="!binIchDran"/>
                        </div>
                        <div class="tw-flex tw-flex-col tw-max-w-4xs">
                            <button class="btn btn-outline-primary tw-my-1 tw-mr-6"
                                    v-for="vorbehalt in ich.moeglicheVorbehalte"
                                    @click="vorbehaltSenden(vorbehalt)"
                                    v-text="vorbehalt"
                                    v-if="soli.includes(vorbehalt)"
                                    :disabled="!binIchDran"/>
                        </div>
                        <div class="tw-flex tw-flex-col tw-max-w-2xs">
                            <button class="btn btn-outline-primary tw-my-1"
                                    @click="farbsoliAnzeigen = true"
                                    v-text="'Farbsolo'"
                                    v-if="!farbsoliAnzeigen"
                                    :disabled="!binIchDran"/>
                            <button class="btn btn-outline-primary tw-my-1"
                                    v-for="vorbehalt in ich.moeglicheVorbehalte"
                                    @click="vorbehaltSenden(vorbehalt)"
                                    v-text="vorbehalt"
                                    v-if="farbsoli.includes(vorbehalt) && farbsoliAnzeigen"
                                    :disabled="!binIchDran"/>
                        </div>
                    </div>
                </div>


                <!-- ******** Armut ********* -->
                <div v-if="istPhase(3) && !binIchDran">
                    Die Armut wählt Ihre Karten.
                </div>

                <div v-if="istPhase(3) && binIchDran">
                    Wähle aus, welche Karten du abgeben möchtest!
                    <hand class="tw-mb-0 tw-mt-3"
                          v-if="armutKarten.length > 0"
                          :karten="armutKarten"
                          :armut="true"
                          @armut="armutKarteWechseln($event, 'hand')"/>
                    <button v-if="armutKarten.length === 3"
                            @click="armutAbgeben"
                            class="btn btn-primary">
                        Karten abgeben
                    </button>
                </div>

                <div v-if="istPhase(32) && binIchDran">
                    <p class="tw-font-bold">
                        Willst du die Armut mitnehmen?
                    </p>
                    <button class="btn btn-primary tw-mr-6"
                            @click="armutMitnehmen(true)">
                        Ja!
                    </button>
                    <button class="btn btn-primary tw-ml-6"
                            @click="armutMitnehmen(false)">
                        Nein!
                    </button>
                </div>

                <div v-if="istPhase(33) && binIchDran">
                    Wähle aus, welche Karten du zurückgeben möchtest!
                    <hand class="tw-mb-0 tw-mt-3"
                          v-if="armutKarten.length > 0"
                          :karten="armutKarten"
                          :armut="true"
                          @armut="armutKarteWechseln($event, 'hand')"/>
                    <button v-if="armutKarten.length === 3"
                            @click="armutZurueckgeben"
                            class="btn btn-primary">
                        Karten zurückgeben
                    </button>
                </div>

                <!-- ******** Aktueller Stich ********* -->
                <div v-if="istPhase(4) && !letzterStichEingeblendet">
                    <stich :stich="liveGame.aktuellerStich.karten"
                           :auth-id="authId"
                           :spieler-ids="liveGame.spielerIDs"/>
                </div>


                <!-- ******** Letzter Stich ********* -->
                <div v-if="(istPhase(4) || istPhase(101)) && letzterStichEingeblendet">
                    <stich :stich="liveGame.letzterStich.karten"
                           :auth-id="authId"
                           :spieler-ids="liveGame.spielerIDs"/>
                </div>

                <div v-if="istPhase(101) && !letzterStichEingeblendet">
                    <div v-html="liveGame.wertung"/>
                    <button class="btn btn-primary tw-mt-6"
                            v-if="true"
                            @click="spielStarten">
                        Neues Spiel starten
                    </button>
                </div>

            </div>

            <!-- ******** Hand ********* -->
            <div class="tw-col-start-2 tw-row-start-3 tw-col-span-2 lg:tw-col-start-2 lg:tw-row-start-3 lg:tw-col-span-2 tw-flex tw-items-center tw-p-4">
                <div>
                    <hand class="tw-mb-0 tw-mt-3"
                          v-if="ich.hand !== '' && liveGame.phase > 0"
                          :karten="ich.hand"
                          :armut="binIchDran && (istPhase(3) || istPhase(33))"
                          @karteSpielen="karteSpielen"
                          @armut="armutKarteWechseln($event, 'mitte')"/>
                </div>
            </div>

            <!-- ******** Ich ********* -->
            <div class="tw-col-start-1 tw-row-start-3 lg:tw-col-start-1 tw-flex tw-items-center tw-justify-center tw-py-4">
                <spieler :spieler="getSpieler(0)"
                         :live-game="liveGame">

                    <template v-slot:letzterStich>
                        <button class="btn btn-sm btn-outline-primary tw-my-1"
                                @click="letzterStich"
                                v-text="letzterStichEingeblendet ? 'Ausblenden' : 'Letzter Stich'"/>
                    </template>

                    <template v-slot:ansagen>
                        <div v-if="istPhase(4)">
                            <button class="btn btn-primary btn-sm tw-my-1"
                                    @click="ansage(ich.moeglicheAnAbsage)"
                                    v-if="binIchDran && ich.moeglicheAnAbsage"
                                    v-text="ich.moeglicheAnAbsage">
                            </button>
                        </div>
                    </template>

                </spieler>
            </div>

            <!-- ******** Spieler 1 ********* -->
            <div class="tw-col-start-1 tw-row-start-1 lg:tw-row-start-2 tw-flex tw-items-center tw-justify-center">
                <spieler :spieler="getSpieler(1)"
                         :live-game="liveGame"
                         @letzterStich="letzterStich">
                    <template v-slot:letzterStich>
                        <button class="btn btn-sm btn-outline-primary tw-my-1"
                                @click="letzterStich"
                                v-text="letzterStichEingeblendet ? 'Ausblenden' : 'Letzter Stich'"/>
                    </template>
                </spieler>
            </div>

            <!-- ******** Spieler 2 ********* -->
            <div class="tw-col-start-2 tw-row-start-1 tw-flex tw-items-center tw-justify-center">
                <spieler :spieler="getSpieler(2)"
                         :live-game="liveGame"
                         @letzterStich="letzterStich">
                    <template v-slot:letzterStich>
                        <button class="btn btn-sm btn-outline-primary tw-my-1"
                                @click="letzterStich"
                                v-text="letzterStichEingeblendet ? 'Ausblenden' : 'Letzter Stich'"/>
                    </template>
                </spieler>
            </div>

            <!-- ******** Spieler 3 ********* -->
            <div class="tw-col-start-3 tw-row-start-1 lg:tw-row-start-2 tw-flex tw-items-center tw-justify-center">
                <spieler :spieler="getSpieler(3)"
                         :live-game="liveGame"
                         @letzterStich="letzterStich">
                    <template v-slot:letzterStich>
                        <button class="btn btn-sm btn-outline-primary tw-my-1"
                                @click="letzterStich"
                                v-text="letzterStichEingeblendet ? 'Ausblenden' : 'Letzter Stich'"/>
                    </template>
                </spieler>
            </div>
        </div>

        <div class="tw-flex-1" v-if="!aktiv">
            Du setzt dieses Spiel aus!
        </div>

        <hr class="tw-mt-8">
        <div class="tw-flex tw-justify-around tw-items-center">
            <span v-if="liveGame !== 'null'">LiveGameID: {{ liveGame.id }}</span>
            <button class="btn btn-primary"
                    v-if="true"
                    @click="spielStarten">
                Neues Spiel starten
            </button>
        </div>
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
                error: '',
                soli: ['Fleischlos', 'Bubensolo', 'Damensolo', 'Königssolo'],
                farbsoli: ['Trumpfsolo', 'Farbsolo Herz', 'Farbsolo Pik', 'Farbsolo Kreuz'],
                farbsoliAnzeigen: false,
                letzterStichEingeblendet: false,
                armutKarten: [],
            }
        },

        created() {
            this.liveGame = this.liveGameInit;
            this.ich = this.ichInit;
            this.ich.hand = Object.values(this.ich.hand);
            this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);

            this.presenceChannel
                .here(playerIDs => {
                    this.liveGame.spielerIDs.forEach(spielerID => {
                        if (!this.pluck(playerIDs, 'id').includes(spielerID)) {
                            this.liveGame.spieler[spielerID].online = false;
                        }
                    });

                    this.players = this.pluck(playerIDs, 'id');
                })
                .joining(playerID => {
                    this.players.push(playerID.id);
                    this.liveGame.spieler[playerID.id].online = true;
                })
                .leaving(playerID => {
                    this.players.splice(this.players.indexOf(playerID.id), 1);
                    this.liveGame.spieler[playerID.id].online = false;
                });

            if (this.aktiv) {
                this.privateChannel
                    .listen('LiveGameDataBroadcasted', e => {
                        this.ich = e.ich;
                        this.ich.hand = Object.values(this.ich.hand);
                        this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);
                        this.liveGame = e.liveGame;
                        this.error = '';

                        this.liveGame.spielerIDs.forEach(spielerID => {
                            if (!this.players.includes(spielerID)) {
                                this.liveGame.spieler[spielerID].online = false;
                            }
                        });

                        if (this.liveGame.aktuellerStich.karten.length === 0) {
                            this.letzterStichEingeblendet = true
                            setTimeout(() => this.letzterStichEingeblendet = false, 2555)
                        }
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

            alleSpielerOnline() {
                return this.roundPlayersIds
                    .every(e => this.players.includes(e));
            },

            binIchDran() {
                return this.liveGame.dran === this.authId;
            },

            reOderKontra() {
                if (this.ich.isRe === true) {
                    return 'Re';
                } else if (this.ich.isRe === false) {
                    return 'Kontra';
                } else {
                    return 'Keins von beidem Fehler';
                }
            },
        },

        watch: {},

        methods: {
            pluck(array, key) {
                return array.map(o => o[key]);
            },

            istPhase(phase) {
                return this.liveGame.phase === phase;
            },

            spielStarten() {
                axios.post('/api/live/' + this.liveRoundId + '/spielStarten', {});
            },

            kartenGeben() {
                axios.post('/api/live/' + this.liveGame.id + '/kartenGeben', {});
            },

            vorbehaltSenden(vorbehalt) {
                axios.post('/api/live/' + this.liveGame.id + '/vorbehalt', {
                    vorbehalt: vorbehalt
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

            ansage(ansage) {
                axios.post('/api/live/' + this.liveGame.id + '/ansage', {
                    ansage: ansage
                })
                    .catch(error => this.handleError(error));
            },

            reloadData() {
                axios.get('/api/live/' + this.liveGame.id + '/reloadData')
                    .then(response => {
                        this.ich = response.data.ich;
                        this.liveGame = response.data.liveGame;

                        this.ich.hand = Object.values(this.ich.hand);
                        this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);

                        this.liveGame.spielerIDs.forEach(spielerID => {
                            if (!this.players.includes(spielerID)) {
                                this.liveGame.spieler[spielerID].online = false;
                            }
                        });
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
                    this.error = error.response.data.message;
                    this.reloadData();
                }
            },

            getSpieler(position) {
                let eigenerIndex = this.liveGame.spielerIDs.indexOf(this.authId);

                let ergebnis = eigenerIndex + position;
                if (ergebnis >= 4) {
                    ergebnis -= 4;
                }
                let spielerID = this.liveGame.spielerIDs[ergebnis];

                return this.liveGame.spieler[spielerID];
            },

            letzterStich() {
                this.letzterStichEingeblendet = !this.letzterStichEingeblendet;
            },

            armutKarteWechseln(karte, richtung) {
                if (richtung === 'mitte') {
                    if (this.armutKarten.length < 3) {
                        let index = this.ich.hand.indexOf(karte);
                        this.ich.hand.splice(index, 1);
                        karte.index = index;
                        this.armutKarten.push(karte);
                    }
                }
                if (richtung === 'hand') {
                    this.armutKarten.splice(this.armutKarten.indexOf(karte), 1);
                    this.ich.hand.splice(karte.index, 0, karte);
                }
            },

            armutAbgeben() {
                axios.post('/api/live/' + this.liveGame.id + '/armutAbgeben', {
                    karten: this.armutKarten
                })
                    .then(this.armutKarten = []);
            },

            armutMitnehmen(mitnehmen) {
                axios.post('/api/live/' + this.liveGame.id + '/armutMitnehmen', {
                    mitnehmen: mitnehmen
                });
            },

            armutZurueckgeben(mitnehmen) {
                axios.post('/api/live/' + this.liveGame.id + '/armutZurueck', {
                    karten: this.armutKarten
                })
                    .then(this.armutKarten = []);
            }
        }
    };
</script>

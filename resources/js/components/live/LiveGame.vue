<template>
    <div class="playingCards">

        <!-- ******** Buttons links ********* -->
        <div class="tw-absolute live-overlay tw-p-3 tw-m-2 tw-left-0 tw-text-3xl tw-flex tw-flex-col"
             style="top: 50%; transform: translate(0, -50%);">
            <i class="fas fa-info-circle tw-cursor-pointer"
               :class="{'tw-text-orange-500': infoEingeblendet}"
               @click="infoEingeblendet = !infoEingeblendet"></i>
            <i class="fas fa-history tw-cursor-pointer tw-my-4"
               :class="{'tw-text-orange-500': letzterStichEingeblendet}"
               @click="letzterStich"></i>
            <i class="fas fa-plus-circle tw-cursor-pointer"
               @click="$emit('neues-spiel-starten')"></i>
        </div>

        <!-- ******** Messages ********* -->
        <div v-if="infoEingeblendet"
             class="tw-absolute live-overlay tw-px-3 tw-pt-2 tw-pb-1 tw-m-2 md:tw-text-sm tw-text-xs tw-flex tw-flex-col tw-w-1/5 tw-text-left tw-h-40 tw-overflow-y-auto"
             style="top: 50%; right:0; transform: translate(0, -50%);">
            <ul class="tw-mb-0">
                <li v-for="(message, index) in liveGame.messages"
                    :key="message"
                    :id="index === liveGame.messages.length - 1 ? 'lastMessage' : ''"
                    v-html="message"
                    class="tw-mb-1"/>
            </ul>
        </div>

        <!-- ******** Spieler ********* -->
        <spieler :round="round" :liveGame="liveGame" :ich="ich"
                 @ansage="ansage"/>

        <!-- ******** Hand ********* -->
        <div style="position: absolute; left: 31%; bottom: 0; transform: translate(-50%);" class="tw--mb-12">
            <hand class="tw-mb-0 tw-mt-3"
                  v-if="ich.hand !== '' && liveGame.phase > 0"
                  :karten="ich.hand"
                  :armut="binIchDran && (istPhase(3) || istPhase(33))"
                  @karteSpielen="karteSpielen"
                  @armut="armutKarteWechseln($event, 'mitte')"/>
        </div>

        <div v-if="(istPhase(4) || istPhase(101))"
             style="position: absolute; top: 46%; left: 50%; transform: translate(-50%, -50%);">
            <!-- ******** Aktueller Stich ********* -->
            <div v-if="istPhase(4) && !letzterStichEingeblendet">
                <stich :stich="liveGame.aktuellerStich.karten"
                       :round="round"/>
            </div>
            <!-- ******** Letzter Stich ********* -->
            <div v-if="(istPhase(4) || istPhase(101)) && letzterStichEingeblendet">
                <stich :stich="liveGame.letzterStich.karten"
                       :round="round"/>
            </div>
        </div>

        <!--<p v-if="error !== ''" v-text="error" class="tw-font-bold tw-text-red-700 tw-my-4"></p> -->

        <!-- ******** Vorbehalte ********* -->
        <div v-if="istPhase(2)" class="center-absolute live-overlay tw-p-2 tw-pt-1">
            <div v-if="istPhase(2) && ich.vorbehalt != null" class="tw-font-bold">
                Bitte warte, bis alle Spieler ihren Vorbehalt bestimmt haben.
            </div>

            <div v-if="istPhase(2) && ich.vorbehalt == null"
                 class="tw-flex tw-flex-col tw-justify-between tw-items-center">

                <p v-if="binIchDran" class="tw-font-bold tw-mb-1">Wähle deinen Vorbehalt:</p>
                <p v-else class="tw-font-bold tw-mb-1">Mögliche Vorbehalte:</p>

                <div class="tw-flex tw-items-center">
                    <button class="btn btn-primary tw-mr-6 tw-max-w-4xs"
                            :disabled="!binIchDran"
                            :class="{'btn-sm': mobile}"
                            v-if="!ich.moeglicheVorbehalte.includes('Hochzeit')"
                            @click="vorbehaltSenden('Gesund')">
                        Gesund
                    </button>
                    <div class="tw-flex tw-flex-col">
                        <button class="btn btn-primary tw-my-1 tw-mr-6"
                                :class="{'btn-sm': mobile}"
                                v-for="vorbehalt in ich.moeglicheVorbehalte"
                                @click="vorbehaltSenden(vorbehalt)"
                                v-text="vorbehalt"
                                v-if="!soli.includes(vorbehalt) && !farbsoli.includes(vorbehalt)"
                                :disabled="!binIchDran"/>
                    </div>
                    <div class="tw-flex tw-flex-col tw-max-w-4xs">
                        <button class="btn btn-primary tw-my-1 tw-mr-6"
                                :class="{'btn-sm': mobile}"
                                v-for="vorbehalt in ich.moeglicheVorbehalte"
                                @click="vorbehaltSenden(vorbehalt)"
                                v-text="vorbehalt"
                                v-if="soli.includes(vorbehalt)"
                                :disabled="!binIchDran"/>
                    </div>
                    <div class="tw-flex tw-flex-col tw-max-w-2xs">
                        <button class="btn btn-primary tw-my-1"
                                :class="{'btn-sm': mobile}"
                                @click="farbsoliAnzeigen = true"
                                v-text="'Farbsolo'"
                                v-if="!farbsoliAnzeigen"
                                :disabled="!binIchDran"/>
                        <button class="btn btn-primary tw-my-1"
                                :class="{'btn-sm': mobile}"
                                v-for="vorbehalt in ich.moeglicheVorbehalte"
                                @click="vorbehaltSenden(vorbehalt)"
                                v-text="vorbehalt"
                                v-if="farbsoli.includes(vorbehalt) && farbsoliAnzeigen"
                                :disabled="!binIchDran"/>
                    </div>
                </div>
            </div>
        </div>


        <!-- ******** Armut ********* -->
        <div v-if="(istPhase(3) || istPhase(32) || istPhase(33))" class="live-overlay tw-p-3"
             style="position: absolute; top: 46%; left: 50%; transform: translate(-50%, -50%);">
            <div v-if="istPhase(3) && !binIchDran">
                Die Armut wählt Ihre Karten.
            </div>

            <div v-if="istPhase(3) && binIchDran">
                Wähle aus, welche Karten du abgeben möchtest!
                <hand class="tw-mb-0 tw-mt-6"
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
        </div>

        <!--<div class="tw-flex-1" v-if="!aktiv">
            Du setzt dieses Spiel aus!
        </div>-->
    </div>
</template>

<script>

export default {
    props: {
        round: Object,
        mobile: Boolean,
    },

    data() {
        return {
            liveGame: 'null',
            oldLiveGame: '',
            ich: 'null',
            error: '',
            soli: ['Fleischlos', 'Bubensolo', 'Damensolo', 'Königssolo'],
            farbsoli: ['Trumpfsolo', 'Farbsolo Herz', 'Farbsolo Pik', 'Farbsolo Kreuz'],
            farbsoliAnzeigen: false,
            letzterStichEingeblendet: false,
            infoEingeblendet: false,
            armutKarten: [],
            infoTimeout: '',
            stichTimeout: '',
        }
    },

    created() {
        this.copyDataFromProp();

        if (this.aktiv) {
            this.privateChannel
                .listen('LiveGameDataBroadcasted', e => {
                    this.liveGame = e.liveGame;
                    this.ich = e.ich;
                    this.ich.hand = Object.values(this.ich.hand);
                    this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);
                    this.error = '';

                    if (this.liveGame.aktuellerStich.karten.length === 0 &&
                        this.oldLiveGame.aktuellerStich.karten.length !== this.liveGame.aktuellerStich.karten.length) {
                        this.letzterStichEingeblendet = true;
                        clearTimeout(this.stichTimeout);
                        this.stichTimeout = setTimeout(() => this.letzterStichEingeblendet = false, 2000)
                    }
                    if (this.liveGame.messages.length >= 0 &&
                        this.oldLiveGame.messages.length !== this.liveGame.messages.length) {
                        this.infoEingeblendet = true
                        clearTimeout(this.infoTimeout);
                        this.infoTimeout = setTimeout(() => this.infoEingeblendet = false, 2000)
                    }
                    this.oldLiveGame = e.liveGame;
                });
        } else {
            this.privateChannel
                .listen('LiveGameDataBroadcastedInaktiv', e => {
                    this.liveGame = e.liveGame;
                });
        }
    },

    computed: {
        privateChannel() {
            return window.Echo
                .private('liveRound.' + this.round.live_round.id + '.' + this.round.auth_id);
        },

        aktiv() {
            return this.pluck(this.round.active_players, 'id').includes(this.round.auth_id);
        },

        binIchDran() {
            return this.liveGame.dran === this.round.auth_id;
        },
    },

    watch: {},

    methods: {
        copyDataFromProp() {
            this.liveGame = this.round.current_live_game;
            this.oldLiveGame = this.round.current_live_game;
            this.ich = this.round.ich;
            this.ich.hand = Object.values(this.ich.hand);
            this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);
        },

        istPhase(phase) {
            return this.liveGame.phase === phase;
        },

        vorbehaltSenden(vorbehalt) {
            axios.post('/api/live/' + this.liveGame.id + '/vorbehalt', {
                vorbehalt: vorbehalt
            })
                .catch(error => this.handleError(error));
        },

        karteSpielen(karte) {
            if (this.binIchDran) {
                this.ich.hand.splice(this.ich.hand.indexOf(karte), 1);
                let karteKopie = karte;
                karteKopie.gespieltVon = this.round.auth_id;
                karteKopie.spielbar = false;
                this.liveGame.aktuellerStich.karten.push(karteKopie);

                axios.post('/api/live/' + this.liveGame.id + '/karteSpielen', {
                    karte: karte
                })
                    .then(response => this.error = '')
                    .catch(error => this.handleError(error));
            }
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

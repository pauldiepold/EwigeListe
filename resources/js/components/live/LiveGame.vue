<template>
    <div class="playingCards">

        <!-- ******** Buttons links ********* -->
        <div
            class="tw-absolute live-overlay md:tw-p-3 tw-p-2 tw-m-2 tw-left-0 md:tw-text-3xl tw-text-2xl tw-flex tw-flex-col"
            style="top: 50%; transform: translate(0, -50%);">
            <div v-if="zuschauerEingeblendet && round.watching_players.length !== 0" class="tw-border-b-2 tw-mb-3">
                <div v-for="player in round.watching_players"
                     class="tw-mb-2">
                    <img :src="player.avatar_path"
                         class="tw-mx-auto md:tw-h-10 md:tw-w-10 tw-h-8 tw-w-8 tw-rounded-full">
                </div>
            </div>
            <!--<i class="fas fa-users tw-cursor-pointer"
               :class="{'tw-text-orange-500': zuschauerEingeblendet}"
               @click="zuschauerEingeblendet = !zuschauerEingeblendet"></i>-->
            <i class="fas fa-history tw-cursor-pointer"
               :class="{'tw-text-orange-500': letzterStichEingeblendet}"
               @click="letzterStich"></i>
            <i class="fas fa-info-circle tw-cursor-pointer md:tw-mt-4 tw-mt-3"
               :class="{'tw-text-orange-500': infoEingeblendet}"
               @click="infoEingeblendet = !infoEingeblendet"></i>
            <i class="fas fa-sync tw-cursor-pointer md:tw-mt-4 tw-mt-3" @click="$emit('reload-live-game')"></i>
            <!--<i class="fas fa-plus-circle tw-cursor-pointer md:tw-mt-4 tw-mt-3"
               @click="$emit('neues-spiel-starten')"></i>-->
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

        <!-- ******** An- und Absagen ********* -->
        <div v-if="aktiv && liveGame.phase === 4"
             style="position: absolute; left: 0; top: 0%; transform: translate(0, 0%);">
            <button class="btn btn-primary tw-mt-3 tw-ml-3"
                    @click="ansage(ich.moeglicheAnAbsage)"
                    v-if="liveGame.dran === round.auth_id && ich.moeglicheAnAbsage"
                    v-text="ich.moeglicheAnAbsage">
            </button>
        </div>

        <!-- ******** Spieler ********* -->
        <spieler :round="round" :liveGame="liveGame"/>

        <!-- ******** Hand ********* -->
        <div v-if="aktiv" style="position: absolute; left: 31%; bottom: 0; transform: translate(-50%);"
             class="tw--mb-12">
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

        <!--<div v-if="error !== ''" v-text="error" class="center-absolute tw-text-gray-200 tw-bg-white tw-bg-opacity-50 tw-rounded-xl tw-p-6 tw-z-50"/>-->

        <!-- ******** Vorbehalte ********* -->

        <div v-if="pluck(round.inactive_players, 'id').includes(round.auth_id) && istPhase(2)"
             class="center-absolute live-overlay tw-py-3 tw-px-4 tw-font-bold">
            Du setzt dieses Spiel aus!
        </div>
        <div v-if="aktiv && istPhase(2)" class="center-absolute live-overlay tw-p-2 tw-pt-1">
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
        <div v-if="aktiv && ((istPhase(3) || ((istPhase(32) || istPhase(33)) && binIchDran)))"
             class="live-overlay tw-p-3"
             style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div v-if="istPhase(3) && !binIchDran">
                Die Armut wählt Ihre Karten.
            </div>

            <div v-if="istPhase(3) && binIchDran">
                <div>Wähle aus, welche Karten du abgeben möchtest!</div>
                <div v-if="armutError" class="tw-font-bold tw-text-red-600">Du musst all deinen Trumpf abgeben!</div>
                <hand class="tw-mb-0 tw-mt-6" style="margin-left: 26.2%;"
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
                <hand class="tw-mb-0 tw-mt-3" style="margin-left: 28.5%;"
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
        aktiv: Boolean,
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
            zuschauerEingeblendet: true,
            armutKarten: [],
            armutError: false,
            infoTimeout: '',
            stichTimeout: '',
        }
    },

    created() {
        this.saveNewData(this.round.current_live_game, this.round.ich, true);

        if (this.aktiv) {
            this.privateChannel
                .listen('LiveGameDataBroadcasted', e => {
                    this.saveNewData(e.liveGame, e.ich);
                });
        } else {
            this.privateChannelZuschauer
                .listen('LiveGameDataBroadcastedInaktiv', e => {
                    this.saveNewData(e.liveGame);
                });
        }
    },

    computed: {
        privateChannelZuschauer() {
            return window.Echo
                .private('liveRound.' + this.round.live_round.id);
        },

        privateChannel() {
            return window.Echo
                .private('liveRound.' + this.round.live_round.id + '.' + this.round.auth_id);
        },

        binIchDran() {
            return this.liveGame.dran === this.round.auth_id;
        },
    },

    watch: {},

    methods: {
        copyDataFromProp() {
            this.saveNewData(this.round.current_live_game, this.round.ich, true);
        },

        saveNewData(liveGame, ich = null, old = false) {
            this.liveGame = liveGame;
            this.error = '';

            if (old) this.oldLiveGame = liveGame;

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
                this.infoTimeout = setTimeout(() => this.infoEingeblendet = false, 4000)
            }

            if (!old) this.oldLiveGame = liveGame;

            if (this.aktiv) {
                this.ich = ich;
                this.ich.hand = Object.values(this.ich.hand);
                this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);
            }
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
                this.$emit('reload-live-game');
            }
        },

        istPhase(phase) {
            return this.liveGame.phase === phase;
        },

        ansage(ansage) {
            axios.post('/api/live/' + this.liveGame.id + '/ansage', {
                ansage: ansage
            })
                .catch(error => this.handleError(error));
        },

        letzterStich() {
            this.letzterStichEingeblendet = !this.letzterStichEingeblendet;
        },

        vorbehaltSenden(vorbehalt) {
            axios.post('/api/live/' + this.liveGame.id + '/vorbehalt', {
                vorbehalt: vorbehalt
            })
                .catch(error => this.handleError(error));
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
            if (this.ich.hand.filter(karte => karte.trumpf).length !== 0) {
                this.armutError = true;
                return;
            } else {
                this.armutError = false;
            }
            axios.post('/api/live/' + this.liveGame.id + '/armutAbgeben', {
                karten: this.armutKarten
            })
                .then(this.armutKarten = [])
                .catch(error => this.handleError(error));
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
    }
};
</script>

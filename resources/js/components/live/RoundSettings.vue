<template>
    <div class="tw-text-left tw-max-w-lg tw-mx-auto">
        <!-- ----- Super-Schwein ------ -->
        <div class="tw-rounded-lg tw-p-4 tw-bg-white tw-mb-4">
            <div class="tw-flex tw-justify-between tw-items-center">
                <div>
                    Super-Schwein
                    <small class="form-text text-muted">
                        Der erste Fuchs wird als Fuchs gespielt, der zweite ist als Super-Schwein die höchste Karte im
                        Spiel
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x tw-text-gray-700" @click="schweinchen = !schweinchen"
                       :class="{'fa-toggle-on tw-text-orange-500': schweinchen, 'fa-toggle-off': !schweinchen}"/>
                </div>
            </div>
            <div v-if="schweinchen" class="tw-flex tw-justify-between tw-items-center tw-mt-4">
                <div>
                    Super-Schwein: Fuchs muss stechen
                    <small class="form-text text-muted">
                        Der erste Fuchs muss den Stich gewinnen, damit der zweite zum Super-Schwein wird
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x tw-text-gray-700" @click="fuchsSticht = !fuchsSticht"
                       :class="{'fa-toggle-on tw-text-orange-500': fuchsSticht, 'fa-toggle-off': !fuchsSticht}"/>
                </div>
            </div>
            <div v-if="schweinchen" class="tw-flex tw-justify-between tw-items-center tw-mt-4">
                <div>
                    Super-Schwein: auch im Trumpfsolo
                    <small class="form-text text-muted">
                        Farbsolo-Karo, nicht in anderen Farbsoli
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x tw-text-gray-700" @click="schweinchenTrumpfsolo = !schweinchenTrumpfsolo"
                       :class="{'fa-toggle-on tw-text-orange-500': schweinchenTrumpfsolo, 'fa-toggle-off': !schweinchenTrumpfsolo}"/>
                </div>
            </div>
        </div>

        <!-- ----- Königssolo ------ -->
        <div class="tw-rounded-lg tw-p-4 tw-bg-white tw-mb-4">
            <div class="tw-flex tw-justify-between tw-items-center">
                <div>
                    Mit Königssolo
                    <small class="form-text text-muted">
                        Nur die Könige sind Trumpf
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x tw-text-gray-700" @click="koenigsSolo = !koenigsSolo"
                       :class="{'fa-toggle-on tw-text-orange-500': koenigsSolo, 'fa-toggle-off': !koenigsSolo}"/>
                </div>
            </div>
        </div>

        <!-- ----- Karlchen ------ -->
        <div class="tw-rounded-lg tw-p-4 tw-bg-white tw-mb-4">
            <div class="tw-flex tw-justify-between tw-items-center">
                <div>
                    Karlchen
                    <small class="form-text text-muted">
                        Sticht ein Kreuz Bube den letzten Stich, gibt dies einen Extrapunkt
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x tw-text-gray-700" @click="karlchen = !karlchen"
                       :class="{'fa-toggle-on tw-text-orange-500': karlchen, 'fa-toggle-off': !karlchen}"/>
                </div>
            </div>
            <div v-if="karlchen" class="tw-flex tw-justify-between tw-items-center tw-mt-4">
                <div>
                    Karlchen kann gefangen werden
                    <small class="form-text text-muted">
                        Ein gefangener Kreuz Bube im letzten Stich gibt einen Extrapunkt
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x tw-text-gray-700" @click="karlchenFangen = !karlchenFangen"
                       :class="{'fa-toggle-on tw-text-orange-500': karlchenFangen, 'fa-toggle-off': !karlchenFangen}"/>
                </div>
            </div>
        </div>

        <div class="tw-text-center" v-if="pluck(round.players, 'id').includes(round.auth_id)">
            <form @submit.prevent="submit">
                <button type="submit"
                        :disabled="round.games.length !== 0 || round.current_live_game"
                        class="btn btn-primary mt-4 d-flex vertical-align-center mx-auto">
                    <span>
                        <i v-if="loading"
                           class="fa fa-spinner fa-spin text-lg mr-2"
                           style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                    </span>
                    <span>
                        Regeln speichern
                    </span>
                </button>
            </form>
            <small v-if="round.games.length !== 0 || round.current_live_game" class="form-text text-muted">
                Die Regeln können nur vor Beginn der Runde geändert werden.
            </small>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        round: Object,
    },

    data() {
        return {
            loading: false,
            schweinchen: true,
            fuchsSticht: true,
            schweinchenTrumpfsolo: true,
            koenigsSolo: true,
            karlchen: true,
            karlchenFangen: true,
        }
    },

    created() {
        this.copyDataFromProp();
    },

    computed: {},
    methods: {
        copyDataFromProp() {
            this.schweinchen = this.round.live_round.schweinchen;
            this.fuchsSticht = this.round.live_round.fuchsSticht;
            this.schweinchenTrumpfsolo = this.round.live_round.schweinchenTrumpfsolo;
            this.koenigsSolo = this.round.live_round.koenigsSolo;
            this.karlchen = this.round.live_round.karlchen;
            this.karlchenFangen = this.round.live_round.karlchenFangen;
        },
        submit() {
            this.loading = true;

            axios.post('/liveRounds/' + this.round.live_round.id, {
                '_method': 'PATCH',
                'schweinchen': this.schweinchen,
                'fuchsSticht': this.fuchsSticht,
                'schweinchenTrumpfsolo': this.schweinchenTrumpfsolo,
                'koenigsSolo': this.koenigsSolo,
                'karlchen': this.karlchen,
                'karlchenFangen': this.karlchenFangen,
            })
                .then(response => {
                    //console.log(response.data);
                    this.edit = false;
                    this.loading = false;
                })
                .catch(error => {
                    //console.log(error.response.data.errors)
                    this.loading = false;
                });
        },
    }
};
</script>

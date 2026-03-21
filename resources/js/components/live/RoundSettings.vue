<template>
    <div class="text-left max-w-lg mx-auto">
        <!-- ----- Super-Schwein ------ -->
        <div class="rounded-lg p-4 bg-white mb-4">
            <div class="flex justify-between items-center">
                <div>
                    Super-Schwein
                    <small class="form-text text-muted">
                        Der erste Fuchs wird als Fuchs gespielt, der zweite ist als Super-Schwein die höchste Karte im
                        Spiel
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x " @click="changeable ? schweinchen = !schweinchen : ''"
                       :class="{'fa-toggle-on': schweinchen,
                                'fa-toggle-off': !schweinchen,
                                'text-orange-500': schweinchen && changeable,
                                'text-gray-700': !schweinchen && changeable,
                                'text-gray-600': !changeable,
                                'cursor-pointer': changeable}"/>
                </div>
            </div>
            <div v-if="schweinchen" class="flex justify-between items-center mt-4">
                <div>
                    Super-Schwein: Fuchs muss stechen
                    <small class="form-text text-muted">
                        Der erste Fuchs muss den Stich gewinnen, damit der zweite zum Super-Schwein wird
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x " @click="changeable ? fuchsSticht = !fuchsSticht : ''"
                       :class="{'fa-toggle-on': fuchsSticht,
                                'fa-toggle-off': !fuchsSticht,
                                'text-orange-500': fuchsSticht && changeable,
                                'text-gray-700': !fuchsSticht && changeable,
                                'text-gray-600': !changeable,
                                'cursor-pointer': changeable}"/>
                </div>
            </div>
            <div v-if="schweinchen" class="flex justify-between items-center mt-4">
                <div>
                    Super-Schwein: auch im Trumpfsolo
                    <small class="form-text text-muted">
                        Farbsolo-Karo, nicht in anderen Farbsoli
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x " @click="changeable ? schweinchenTrumpfsolo = !schweinchenTrumpfsolo : ''"
                       :class="{'fa-toggle-on': schweinchenTrumpfsolo,
                                'fa-toggle-off': !schweinchenTrumpfsolo,
                                'text-orange-500': schweinchenTrumpfsolo && changeable,
                                'text-gray-700': !schweinchenTrumpfsolo && changeable,
                                'text-gray-600': !changeable,
                                'cursor-pointer': changeable}"/>
                </div>
            </div>
        </div>

        <!-- ----- Königssolo ------ -->
        <div class="rounded-lg p-4 bg-white mb-4">
            <div class="flex justify-between items-center">
                <div>
                    Mit Königssolo
                    <small class="form-text text-muted">
                        Nur die Könige sind Trumpf
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x " @click="changeable ? koenigsSolo = !koenigsSolo : ''"
                       :class="{'fa-toggle-on': koenigsSolo,
                                'fa-toggle-off': !koenigsSolo,
                                'text-orange-500': koenigsSolo && changeable,
                                'text-gray-700': !koenigsSolo && changeable,
                                'text-gray-600': !changeable,
                                'cursor-pointer': changeable}"/>
                </div>
            </div>
        </div>

        <!-- ----- Karlchen ------ -->
        <div class="rounded-lg p-4 bg-white mb-4">
            <div class="flex justify-between items-center">
                <div>
                    Karlchen
                    <small class="form-text text-muted">
                        Sticht ein Kreuz Bube den letzten Stich, gibt dies einen Extrapunkt
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x " @click="changeable ? karlchen = !karlchen : ''"
                       :class="{'fa-toggle-on': karlchen,
                                'fa-toggle-off': !karlchen,
                                'text-orange-500': karlchen && changeable,
                                'text-gray-700': !karlchen && changeable,
                                'text-gray-600': !changeable,
                                'cursor-pointer': changeable}"/>
                </div>
            </div>
            <div v-if="karlchen" class="flex justify-between items-center mt-4">
                <div>
                    Karlchen kann gefangen werden
                    <small class="form-text text-muted">
                        Ein gefangener Kreuz Bube im letzten Stich gibt einen Extrapunkt
                    </small>
                </div>
                <div class=" ml-4">
                    <i class="fas fa-2x " @click="changeable ? karlchenFangen = !karlchenFangen : ''"
                       :class="{'fa-toggle-on': karlchenFangen,
                                'fa-toggle-off': !karlchenFangen,
                                'text-orange-500': karlchenFangen && changeable,
                                'text-gray-700': !karlchenFangen && changeable,
                                'text-gray-600': !changeable,
                                'cursor-pointer': changeable}"/>
                </div>
            </div>
        </div>

        <div class="text-center" v-if="round.players.map(v => v.id).includes(round.auth_id)">
            <form @submit.prevent="submit">
                <button type="submit"
                        :disabled="!changeable"
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
            <small v-if="!changeable" class="form-text text-muted">
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

    computed: {
        changeable() {
            return this.round.games.length === 0 && !this.round.current_live_game
        }
    },
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
            if (!this.karlchen) {
                this.karlchenFangen = false
            }

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

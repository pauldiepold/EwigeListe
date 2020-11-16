<template>
    <div>
        <button type="button" id="createGameButton" class="btn btn-primary tw-my-1" data-toggle="modal"
                data-target="#createGame">
            Spiel eintragen
        </button>

        <div class="modal" id="createGame" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content disable-dbl-tap-zoom">
                    <div class="modal-header">
                        <h5 class="modal-title mx-auto" id="createModalLabel">Welche Spieler haben gewonnen?</h5>
                    </div>
                    <div class="modal-body">

                        <form @submit.prevent="submit">

                            <div v-for="player in round.active_players" :key="player.id"
                                 class="custom-control custom-checkbox my-1">
                                <input class="custom-control-input" type="checkbox" :value="player.id"
                                       :id="'player' + player.id" name="winners[]" v-model="form.winners">
                                <label class="custom-control-label font-weight-bold" :for="'player' + player.id">
                                    {{ player.surname }} {{ player.name }}
                                </label>
                            </div>

                            <div class="form-row my-4 mx-auto justify-content-center tw-flex">
                                <div class="tw-mr-3 tw-mt-1">
                                    <i class="fas fa-minus-circle tw-text-3xl tw-text-blue-dark tw-cursor-pointer"
                                    @click="form.points--"></i>
                                </div>
                                <div class="col-xs-6 col-xs-offset-3">
                                    <input class="form-control"
                                           type="number" min="-4" max="16" v-model="form.points" id="points">
                                    <label for="points" class="control-label font-weight-bold">Punkte</label>
                                </div>
                                <div class="tw-ml-3 tw-mt-1">
                                    <i class="fas fa-plus-circle tw-text-3xl tw-text-blue-dark tw-cursor-pointer"
                                    @click="form.points++"></i>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <div class="d-flex vertical-align-center">
                                    <span v-if="loading">
                                        <i class="fa fa-spinner fa-spin text-lg mr-2"
                                           style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                                    </span>
                                    <span>
                                        Speichern
                                    </span>
                                </div>
                            </button>
                            <hr>
                            <div class="custom-control custom-checkbox my-1">
                                <input class="custom-control-input" type="checkbox" id="misplayed"
                                       v-model="form.misplayed"
                                >
                                <label class="custom-control-label font-weight-bold" for="misplayed">
                                    Falsch bedient?
                                </label>
                            </div>
                            <a data-container="body" data-toggle="popover" data-placement="top" title="Falsch bedient?"
                               data-content="Falls jemand falsch bedient, wird dies als verlorenes Solo mit 2 Punkten plus die getätigten Ansagen gewertet. Dieses Ergebnis wird oben eintragen.">
                                <i class="fas fa-info-circle fa-lg"></i>
                            </a>
                        </form>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary mx-auto" data-dismiss="modal">Schließen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Form from "../../lib/Form";

export default {
    props: ['round'],
    data() {
        return {
            form: new Form({
                points: 0,
                winners: [],
                misplayed: false,
            }),
            loading: false,
        }
    },
    computed: {
        points() {
            return parseInt(this.form.points);
        }
    },
    methods: {
        submit() {
            this.loading = true;
            this.form.post('/api/rounds/' + this.round.id + '/game')
                .then(response => {
                    this.loading = false;
                    this.form.reset();
                    $('#createGameButton').blur();
                    $('#createGame').modal('hide')
                    this.$emit('updated');
                })
                .catch(error => {
                    console.log(error);
                });
        },
        pluck(array, key) {
            return array.map(o => o[key]);
        },
    }
};
</script>

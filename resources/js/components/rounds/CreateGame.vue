<template>
    <div>
        <button type="button" id="createGameButton" class="btn btn-primary tw-my-1" data-toggle="modal"
                data-target="#createGame">
            Spiel eintragen
        </button>

        <div class="modal" id="createGame" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mx-auto" id="createModalLabel">Welche Personen haben gewonnen?</h5>
                    </div>
                    <div class="modal-body">

                        <form @submit.prevent="submit">

                            <div class="tw-flex tw-justify-between tw-items-center tw-flex-col">
                                <div :class="{'tw-shadow-green tw-border-transparent': winners.includes(round.active_players[2].id),
                                              'tw-border-gray-500':  !winners.includes(round.active_players[2].id)}"
                                     @click="toggle(round.active_players[2].id)"
                                     class="tw-flex tw-w-2/5 tw-justify-around tw-items-center tw-rounded-lg tw-cursor-pointer tw-px-2 tw-py-1 tw-border">
                                    <img :src="round.active_players[2].avatar_path"
                                         class="tw-mr-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full">
                                    <div class="tw-font-bold tw-ml-1">
                                        {{ round.active_players[2].surname }} {{ round.active_players[2].name }}
                                    </div>
                                </div>
                                <div class="tw-flex tw-justify-between tw-my-8 tw-w-full">
                                    <div :class="{'tw-shadow-green tw-border-transparent': winners.includes(round.active_players[1].id),
                                              'tw-border-gray-500':  !winners.includes(round.active_players[1].id)}"
                                         @click="toggle(round.active_players[1].id)"
                                         class="tw-flex tw-w-2/5 tw-justify-around tw-items-center tw-rounded-lg tw-cursor-pointer tw-px-2 tw-py-1 tw-border">
                                        <img :src="round.active_players[1].avatar_path"
                                             class="tw-mr-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full">
                                        <div class="tw-font-bold tw-ml-1">
                                            {{ round.active_players[1].surname }} {{ round.active_players[1].name }}
                                        </div>
                                    </div>
                                    <div :class="{'tw-shadow-green tw-border-transparent': winners.includes(round.active_players[3].id),
                                              'tw-border-gray-500':  !winners.includes(round.active_players[3].id)}"
                                         @click="toggle(round.active_players[3].id)"
                                         class="tw-flex tw-w-2/5 tw-justify-around tw-items-center tw-rounded-lg tw-cursor-pointer tw-px-2 tw-py-1 tw-border">
                                        <img :src="round.active_players[3].avatar_path"
                                             class="tw-mr-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full">
                                        <div class="tw-font-bold tw-ml-1">
                                            {{ round.active_players[3].surname }} {{ round.active_players[3].name }}
                                        </div>
                                    </div>
                                </div>
                                <div :class="{'tw-shadow-green tw-border-transparent': winners.includes(round.active_players[0].id),
                                              'tw-border-gray-500':  !winners.includes(round.active_players[0].id)}"
                                     @click="toggle(round.active_players[0].id)"
                                     class="tw-flex tw-w-2/5 tw-justify-around tw-items-center tw-rounded-lg tw-cursor-pointer tw-px-2 tw-py-1 tw-border">
                                    <img :src="round.active_players[0].avatar_path"
                                         class="tw-mr-1 md:tw-h-10 md:tw-w-10 tw-h-7 tw-w-7 tw-rounded-full">
                                    <div class="tw-font-bold tw-ml-1">
                                        {{ round.active_players[0].surname }} {{ round.active_players[0].name }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row tw-mb-4 tw-mt-10 mx-auto justify-content-center tw-flex">
                                <div class="tw-mr-3 tw-mt-1">
                                    <i class="fas fa-minus-circle tw-text-3xl tw-text-gray-800 tw-cursor-pointer"
                                       @click="decrementPoints"></i>
                                </div>
                                <div class="col-xs-6 col-xs-offset-3">
                                    <input class="form-control"
                                           type="number" min="-4" max="16" v-model="points" id="points">
                                    <label for="points" class="control-label font-weight-bold">Punkte</label>
                                </div>
                                <div class="tw-ml-3 tw-mt-1">
                                    <i class="fas fa-plus-circle tw-text-3xl tw-text-gray-800 tw-cursor-pointer"
                                       @click="incrementPoints"></i>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" :disabled="buttonDisabled">
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
                                       v-model="misplayed"
                                >
                                <label class="custom-control-label font-weight-bold" for="misplayed">
                                    Falsch bedient?
                                </label>
                            </div>
                            <small v-if="misplayed" class="form-text text-muted">
                                Falls jemand falsch bedient, wird dies als verlorenes Solo mit 2 Punkten plus die
                                getätigten Ansagen gewertet. Dieses Ergebnis wird oben eintragen.
                            </small>
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
export default {
    props: ['round'],
    data() {
        return {
            points: 0,
            winners: [],
            misplayed: false,
            loading: false,
        }
    },
    computed: {
        buttonDisabled() {
            return ((this.winners.length === 0 || this.winners.length === 4) && !this.misplayed
                || (this.misplayed && this.winners.length !== 3));
        }
    },
    methods: {
        incrementPoints() {
            if (this.points < 16) this.points++;
        },
        decrementPoints() {
            if (this.points > -4) this.points--;
        },
        submit() {
            this.loading = true;
            axios.post('/api/rounds/' + this.round.id + '/game', {
                'points': this.points,
                'winners': this.winners,
                'misplayed': this.misplayed,
            })
                .then(response => {
                    this.loading = false;
                    this.points = 0;
                    this.winners = [];
                    this.misplayed = false;
                    $('#createGame').modal('hide')
                    this.$emit('updated');
                })
                .catch(error => {
                    console.log(error);
                });
        },
        toggle(id) {
            if (this.winners.includes(id)) {
                this.winners.splice(this.winners.indexOf(id), 1);
            } else {
                this.winners.push(id);
            }
        },
    }
};
</script>

<template>
    <div>
        <button type="button" id="createGameButton" class="btn btn-primary my-1"
                @click="modalOpen = true">
            Spiel eintragen
        </button>

        <div v-if="modalOpen"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
             @keydown.escape.window="modalOpen = false">
            <div class="bg-white rounded w-full max-w-lg mx-4 shadow-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mx-auto" id="createModalLabel">Welche Personen haben gewonnen?</h5>
                    </div>
                    <div class="modal-body">

                        <form @submit.prevent="submit">

                            <!-- Einheitliches Grid-Layout für alle Spieler -->
                            <div class="grid grid-cols-4 gap-4 max-w-2xl mx-auto">
                                <!-- Spieler 0 -->
                                <div :class="[
                                    {'shadow-green border-transparent': winners.includes(round.active_players[0].id),
                                     'border-gray-500': !winners.includes(round.active_players[0].id)},
                                    isActivePlayer ? 'order-4 col-start-2' : 'order-3 col-start-1'
                                ]"
                                     @click="toggle(round.active_players[0].id)"
                                     class="col-span-2 flex justify-center items-center rounded-lg cursor-pointer px-2 py-1 border">
                                    <img :src="round.active_players[0].avatar_path"
                                         class="mr-2 md:h-10 md:w-10 h-7 w-7 rounded-full">
                                    <div class="font-bold">
                                        {{ round.active_players[0].surname }} {{ round.active_players[0].name }}
                                    </div>
                                </div>

                                <!-- Spieler 1 -->
                                <div :class="[
                                    {'shadow-green border-transparent': winners.includes(round.active_players[1].id),
                                     'border-gray-500': !winners.includes(round.active_players[1].id)},
                                    isActivePlayer ? 'order-2 col-start-1' : 'order-1 col-start-1'
                                ]"
                                     @click="toggle(round.active_players[1].id)"
                                     class="col-span-2 flex justify-center items-center rounded-lg cursor-pointer px-2 py-1 border">
                                    <img :src="round.active_players[1].avatar_path"
                                         class="mr-2 md:h-10 md:w-10 h-7 w-7 rounded-full">
                                    <div class="font-bold">
                                        {{ round.active_players[1].surname }} {{ round.active_players[1].name }}
                                    </div>
                                </div>

                                <!-- Spieler 2 -->
                                <div :class="[
                                    {'shadow-green border-transparent': winners.includes(round.active_players[2].id),
                                     'border-gray-500': !winners.includes(round.active_players[2].id)},
                                    isActivePlayer ? 'order-1 col-start-2' : 'order-2 col-start-3'
                                ]"
                                     @click="toggle(round.active_players[2].id)"
                                     class="col-span-2 flex justify-center items-center rounded-lg cursor-pointer px-2 py-1 border">
                                    <img :src="round.active_players[2].avatar_path"
                                         class="mr-2 md:h-10 md:w-10 h-7 w-7 rounded-full">
                                    <div class="font-bold">
                                        {{ round.active_players[2].surname }} {{ round.active_players[2].name }}
                                    </div>
                                </div>

                                <!-- Spieler 3 -->
                                <div :class="[
                                    {'shadow-green border-transparent': winners.includes(round.active_players[3].id),
                                     'border-gray-500': !winners.includes(round.active_players[3].id)},
                                    isActivePlayer ? 'order-3 col-start-3' : 'order-4 col-start-3'
                                ]"
                                     @click="toggle(round.active_players[3].id)"
                                     class="col-span-2 flex justify-center items-center rounded-lg cursor-pointer px-2 py-1 border">
                                    <img :src="round.active_players[3].avatar_path"
                                         class="mr-2 md:h-10 md:w-10 h-7 w-7 rounded-full">
                                    <div class="font-bold">
                                        {{ round.active_players[3].surname }} {{ round.active_players[3].name }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row mb-4 mt-10 mx-auto justify-content-center flex">
                                <div class="mr-3 mt-1">
                                    <i class="fas fa-minus-circle text-3xl text-gray-800 cursor-pointer"
                                       @click="decrementPoints"></i>
                                </div>
                                <div class="col-xs-6 col-xs-offset-3">
                                    <input class="form-control"
                                           type="number" min="-4" max="16" v-model="points" id="points">
                                    <label for="points" class="control-label font-weight-bold">Punkte</label>
                                </div>
                                <div class="ml-3 mt-1">
                                    <i class="fas fa-plus-circle text-3xl text-gray-800 cursor-pointer"
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
                        <button type="button" class="btn btn-outline-secondary mx-auto" @click="modalOpen = false">Schließen
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
            modalOpen: false,
            points: 0,
            winners: [],
            misplayed: false,
            loading: false,
        }
    },
    computed: {
        isActivePlayer() {
            return this.round.active_players.map(o => o['id']).includes(this.round.auth_id);
        },
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
                    this.modalOpen = false;
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

<template>
    <div class="form-autocomplete">
        <div v-if="players.length < 7"
             class="bg-white rounded shadow-2 mx-auto my-4 px-3 pb-2 pt-3"
             style="max-width:19rem;">

            <input id="text-search"
                   class="custom-input"
                   :value="textSearch"
                   @input="textSearch = $event.target.value"
                   @focus="scrollTo('#text-search')"
                   ref="textSearch"
                   type="text"
                   placeholder="Bitte Namen eingeben"/>

            <div class="mt-1 tw-h-40 tw-scrolling-touch sm:tw-scrolling-auto tw-overflow-auto">
                <div class="py-2 px-1 text-left sm:hover:tw-bg-gray-200"
                     v-for="(player) in filteredPlayers"
                     @click="addPlayer(player)">
                    {{player.surname.concat(' ', player.name)}}
                </div>
                <div class="py-2 px-1 text-left tw-mt-1" v-if="filteredPlayers.length===0">
                    Spieler wurde nicht gefunden.
                </div>
            </div>
        </div>

        <h5 class="mt-4" v-if="players.length !== 0">
            {{players.length}} Spieler:
        </h5>


        <sortable-players-list lockAxis="y"
                               :useDragHandle="true"
                               v-model="players">
            <sortable-player v-for="(player, index) in players"
                             v-bind:key="player.id"
                             :players="players"
                             :player="player"
                             :index="index"
                             @remove-player="removePlayer"/>
        </sortable-players-list>


        <form @submit.prevent="onSubmit">
            <button :disabled="players.length < 4"
                    type="submit"
                    class="btn btn-primary mt-4 d-flex vertical-align-center mx-auto">
                <span>
                    <i v-if="loading"
                       class="fa fa-spinner fa-spin text-lg mr-2"
                       style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                </span>
                <span>
                    Neue Runde starten
                </span>
            </button>
        </form>

        <h5 class="mt-5" v-if="players.length !== 0">Runde wird {{ groupText }} hinzugefügt:</h5>

        <div v-for="group in filteredGroups"
             @click="inSelectedGroups(group) ? removeGroup(group) : addGroup(group)"
             class="text-left d-flex align-items-center justify-content-between tw-cursor-pointer group"
             style="max-width: 24rem;"
             id="groups">
            <span class="font-weight-bold"
                  :class="{'tw-text-gray-500': !inSelectedGroups(group)}">
                {{group.name}}
            </span>
            <i class="fas fa-2x mx-1 tw-text-gray-600"
               :class="{'fa-toggle-on': inSelectedGroups(group), 'fa-toggle-off': !inSelectedGroups(group)}">
            </i>
        </div>

    </div>
</template>

<script>
    import SortablePlayer from "./SortablePlayer";
    import SortablePlayersList from "./SortablePlayersList";

    export default {
        components: {
            SortablePlayer,
            SortablePlayersList,
        },

        props: {
            allPlayers: {
                type: Array,
                default() {
                    return [];
                }
            }
        },
        data() {
            return {
                textSearch: '',
                loading: false,
                groups: [],
                players: []
            }
        },

        computed: {
            filteredPlayers() {
                return this.allPlayers.filter(player => this.fullName(player).toLowerCase().includes(this.textSearch.toLowerCase())
                    && !this.inSelectedPlayers(player));
            },
            filteredGroups() {
                let output = [];

                this.players.forEach(function (player) {
                    player.groups.forEach(function (group) {
                        if (!output.map(v => v.id).includes(group.id) && group.id !== 1) {
                            output.push(group);
                        }
                    });
                });

                output.sort((a, b) => a.id - b.id);

                return output;
            },
            groupText() {
                if (this.groups.length === 0) {
                    return 'keiner Gruppe';
                } else if (this.groups.length === 1) {
                    return 'einer Gruppe';
                } else {
                    return this.groups.length + ' Gruppen'
                }
            }
        },
        mounted() {
            this.focusTextSearch();
        },
        methods: {
            scrollTo(element) {
                this.$scrollTo(element);
            },
            fullName(player) {
                return player.surname.concat(' ', player.name);
            },
            inSelectedPlayers(player) {
                return this.players.includes(player);
            },
            inSelectedGroups(group) {
                return this.groups.includes(group);
            },
            addPlayer(player) {
                if (!this.inSelectedPlayers(player)) {
                    if (this.textSearch !== '') {
                        this.$refs.textSearch.focus();
                    }
                    this.textSearch = '';
                    this.players.push(player);
                }
            },
            removePlayer(player) {
                this.textSearch = '';
                let index = this.players.indexOf(player);
                this.players.splice(index, 1);
            },
            addGroup(group) {
                if (!this.inSelectedGroups(group)) {
                    this.groups.push(group);
                }
            },
            removeGroup(group) {
                let index = this.groups.indexOf(group);
                if (index > -1) {
                    this.groups.splice(index, 1);
                }
            },
            focusTextSearch() {
                this.$refs.textSearch.focus();
            },
            onSubmit() {
                this.submit()
                    .then(response => window.location.href = response)
                    .catch(errors => console.log(errors));
            },
            submit() {
                this.loading = true;
                return new Promise((resolve, reject) => {
                    axios.post('/rounds', {
                        'players': this.players.map(v => v.id),
                        'groups': this.groups.map(v => v.id)
                    })
                        .then(response => {
                            //this.onSuccess(response.data);

                            resolve(response.data);
                        })
                        .catch(error => {
                            //this.onFail(error.response.data.errors);

                            this.loading = false;
                            reject(error.response);
                        })
                });
            }
        }
    };
</script>

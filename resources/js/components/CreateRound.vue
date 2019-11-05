<template>
    <div class="form-autocomplete">
        <div v-if="players.length < 7"
             class="bg-white tw-rounded-lg shadow-2 mx-auto tw-my-6 tw-p-3"
             style="max-width:19rem;">

            <input id="text-search"
                   ref="textSearch"
                   class="tw-bg-gray-200 tw-rounded-lg tw-w-full tw-px-3 tw-py-2 tw-mb-3 tw-appearance-none focus:tw-outline-none focus:tw-shadow-outline focus:tw-border-purple-500 tw-border-1"
                   :value="textSearch"
                   @input="textSearch = $event.target.value"
                   @focus="scrollTo('#text-search')"
                   type="text"
                   placeholder="Bitte Namen eingeben"/>

            <div class=" tw-h-40 tw-scrolling-touch sm:tw-scrolling-auto tw-overflow-auto tw-bg-gray-200 tw-rounded-lg">
                <div class="tw-px-2 tw-py-1 tw-mx-1 tw-my-2 text-left tw-cursor-pointer"
                     v-for="(player) in filteredPlayers"
                     @click="addPlayer(player)">
                    {{player.surname.concat(' ', player.name)}}
                </div>
                <div class="tw-px-3 tw-py-2 text-left" v-if="filteredPlayers.length===0">
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


        <form @submit.prevent="submit">
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
             class="text-left d-flex align-items-center justify-content-between group"
             :class="{'tw-cursor-pointer': group.id !== 1}"
             style="max-width: 24rem;"
             id="groups">
            <span class="font-weight-bold"
                  :class="{'tw-text-gray-600': !inSelectedGroups(group)}">
                {{group.name}}
            </span>
            <i class="fas fa-2x mx-1 tw-text-gray-700"
               :class="{'fa-toggle-on': inSelectedGroups(group), 'fa-toggle-off': !inSelectedGroups(group)}"
               v-show="group.id !== 1">
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
                groups: [1],
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
                        if (!output.map(v => v.id).includes(group.id)) {
                            output.push(group);
                        }
                    });
                });

                output.sort((a, b) => a.id - b.id);

                return output;
            },
            selectedGroups() {
                let output = [];

                let self = this;
                this.groups.forEach(function (groupID) {
                    if (self.filteredGroups.map(v => v.id).includes(groupID)) {
                        output.push(groupID);
                    }
                });

                return output;
            },
            groupText() {
                if (this.groups.length === 0) {
                    return 'keiner Liste';
                } else if (this.groups.length === 1) {
                    return 'einer Liste';
                } else {
                    return this.groups.length + ' Listen'
                }
            }
        },

        mounted() {
            //this.focusTextSearch();
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
                return this.groups.includes(group.id);
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
                    this.groups.push(group.id);
                }
            },
            removeGroup(group) {
                if (group.id !== 1) {
                    let index = this.groups.indexOf(group.id);
                    if (index > -1) {
                        this.groups.splice(index, 1);
                    }
                }
            },
            focusTextSearch() {
                this.$refs.textSearch.focus();
            },
            submit() {
                this.loading = true;

                axios.post('/rounds', {
                    'players': this.players.map(v => v.id),
                    'groups': this.selectedGroups
                })
                    .then(response => {
                        window.location.href = response.data;
                    })
                    .catch(error => {
                        console.log(error.response.data.errors)

                        this.loading = false;
                    });
            }
        }
    };
</script>

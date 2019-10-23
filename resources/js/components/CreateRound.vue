<template>
    <div class="form-autocomplete">

        <div v-if="players.length < 7"
             class="bg-white rounded shadow-2 mx-auto my-4 px-3 pb-2 pt-3"
             style="max-width:19rem;">

            <input id="text-search"
                   class="custom-input"
                   :value="textSearch"
                   @input="textSearch = $event.target.value"
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

        <h4 class="mt-4" v-if="players.length !== 0">
            {{players.length}} Spieler:
        </h4>


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

        <h4 class="mt-5" v-if="groups.length !== 0">Runde wird diesen {{selectedGroups.length}}
            Gruppen hinzugefügt:</h4>
        <div v-for="(group) in groups"
             @click="inSelectedGroups(group.id) ? deselectGroup(group) : selectGroup(group)"
             class="text-left d-flex align-items-center justify-content-between tw-cursor-pointer group"
             style="max-width: 24rem;">
            <span class="font-weight-bold"
                  :class="{'tw-text-gray-500': !inSelectedGroups(group.id)}">
                {{group.name}}
            </span>
            <i class="fas fa-2x mx-1 tw-text-gray-600"
               :class="{'fa-toggle-on': inSelectedGroups(group.id), 'fa-toggle-off': !inSelectedGroups(group.id)}">
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
                deselectedGroups: [],
                players: []
            }
        },
        //https://proton.efelle.co/#/documentation/components/sortable
        computed: {
            filteredPlayers() {
                return this.allPlayers.filter(player => this.fullName(player).toLowerCase().includes(this.textSearch.toLowerCase()) && !this.inSelectedPlayers(player.id));//.slice(0, 4);
            },
            selectedPlayerIDs() {
                return this.players.map(v => v.id);
            },
            selectedGroupIDs() {
                return this.selectedGroups.map(v => v.id);
            },
            groups() {
                let output1 = [];
                this.players.forEach(function (player) {
                    player.groups.forEach(function (group) {
                        output1.push(group);
                    });
                });

                let output = [];
                let keys = [1];

                output1.forEach(function (group) {
                    let key = group.id;

                    if (keys.indexOf(key) === -1) {
                        keys.push(key);
                        output.push(group);
                    }
                });

                output.sort((a, b) => a.id - b.id);

                return output;
            },

            selectedGroups() {
                return this.groups.filter(x => !this.deselectedGroups.includes(x));
            }
        },
        methods: {
            fullName(player) {
                return player.surname.concat(' ', player.name);
            },
            inSelectedPlayers(id) {
                return this.selectedPlayerIDs.includes(id);
            },
            inSelectedGroups(id) {
                return this.selectedGroupIDs.includes(id);
            },
            moveUp(player) {
                let tempIndex = this.players.indexOf(player);
                this.players.splice(tempIndex, 1);
                this.players.splice(tempIndex - 1, 0, player);
            },
            moveDown(player) {
                let tempIndex = this.players.indexOf(player);
                this.players.splice(tempIndex, 1);
                this.players.splice(tempIndex + 1, 0, player);
            },
            addPlayer(player) {
                if (!this.inSelectedPlayers(player.id)) {
                    this.textSearch = '';
                    this.players.push(player);
                }
            },
            removePlayer(index) {
                this.textSearch = '';
                this.players.splice(index, 1);
            },
            deselectGroup(group) {
                if (this.inSelectedGroups(group.id)) {
                    this.deselectedGroups.push(group);
                }
            },
            selectGroup(group) {
                let index = this.deselectedGroups.indexOf(group);
                if (index > -1) {
                    this.deselectedGroups.splice(index, 1);
                }
            },
            onSubmit() {
                this.submit()
                    .then(response => window.location.href = response)
                    .catch(errors => console.log(errors));
            },
            submit() {
                this.loading = true;
                return new Promise((resolve, reject) => {
                    axios.post('/rounds', {'players': this.selectedPlayerIDs, 'groups': this.selectedGroupIDs})
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

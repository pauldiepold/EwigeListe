<template>
    <div class="form-autocomplete">
        <div class="bg-white rounded shadow-2 mx-auto my-4 px-3 pb-2 pt-3" style="max-width:19rem;"
             v-if="players.length < 7">
            <input id="text-search" class="custom-input" :value="textSearch" @input="textSearch = $event.target.value"
                   type="text" :placeholder="placeholder"/>

            <div class="mt-2 tw-bg-grey">
                <div class="py-2 px-1 text-left sm:hover:tw-bg-gray-200" v-for="(player) in filteredPlayers"
                     @click="addPlayer(player)">
                    {{player.surname.concat(' ', player.name)}}
                </div>
                <div class="py-2 px-1 text-left" v-if="filteredPlayers.length===0">
                    Spieler wurde nicht gefunden.
                </div>
            </div>
        </div>

        <h4 class="mt-4" v-if="players.length !== 0">{{players.length}} Spieler:</h4>
        <transition-group name="flip-list">
            <div
                class="rounded bg-white px-3 py-2 my-3 mx-auto d-flex align-items-center justify-content-between shadow-2"
                style="max-width: 19rem;"
                v-for="(player, key) in players" v-bind:key="player.id">
                <span class="font-weight-bold">
                    {{player.surname.concat(' ', player.name)}}
                </span>
                <span style="font-size: 1.1rem;" class="tw-cursor-default">
                        <i class="fas fa-chevron-up mx-1 text-muted tw-cursor-pointer" @click="moveUp(player)"
                           v-if="key !== 0"></i>
                        <i class="fas fa-chevron-down mx-1 text-muted tw-cursor-pointer" @click="moveDown(player)"
                           v-if="key !== players.length-1"></i>
                        <i class="fas fa-trash mx-1 text-danger tw-cursor-pointer" @click="removePlayer(key)"></i>
                    </span>
            </div>
        </transition-group>

        <h4 class="mt-5" v-if="uniqueGroups.length !== 0">Runde wird diesen {{uniqueGroups.length}}
            Gruppen hinzugefügt:</h4>
        <div class="rounded bg-white px-3 py-2 my-3 mx-auto d-flex align-items-center justify-content-between shadow-2"
             style="max-width: 19rem;" v-for="(group, key) in uniqueGroups" v-bind:key="group.id" @click="deselectGroup(group)">
            <span class="font-weight-bold">
                {{group.name}} - {{ key }}
            </span>
            <span style="font-size: 1.1rem;" class="tw-cursor-default">
                <i class="fas fa-trash mx-1 text-danger tw-cursor-pointer"></i>
            </span>
        </div>

        <form @submit.prevent="onSubmit">
            <button type="submit" class="btn btn-primary mt-4 d-flex vertical-align-center mx-auto"
                    :disabled="players.length < 4">
                 <span>
                    <i v-if="loading" class="fa fa-spinner fa-spin text-lg mr-2"
                       style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
                 </span>
                <span>
                    Neue Runde starten
                 </span>
            </button>
        </form>
    </div>
</template>

<script>
    export default {
        props: {
            allPlayers: {
                type: Array,
                default() {
                    return [];
                }
            },
            placeholder: {
                type: String,
                default: 'Bitte Namen eingeben',
            }
        },
        data() {
            return {
                textSearch: '',
                loading: false,
                players: [],
                deselectedGroups: [],
            }
        },
        computed: {
            filteredPlayers() {
                return this.allPlayers.filter(player => this.fullName(player).toLowerCase().includes(this.textSearch.toLowerCase()) && !this.inSelectedPlayers(player.id)).slice(0, 4);
            },
            selectedPlayers() {
                return this.players.map(v => v.id);
            },
            selectedGroups() {
                return this.uniqueGroups.map(v => v.id);
            },
            groups() {
                let output = [];
                this.players.forEach(function (player) {
                    player.groups.forEach(function (group) {
                        output.push(group);
                    });
                });
                return output;
            },
            uniqueGroups() {
                let output = [];
                let keys = [];

                this.groups.forEach(function (group) {
                    let key = group.id;

                    if (keys.indexOf(key) === -1) {
                        keys.push(key);
                        output.push(group);
                    }
                });

                //.filter(x => !this.deselectedGroups.includes(x))
                return output;
            },
            diffGroups() {
                return this.deselectedGroups;
            }
        },
        methods: {
            fullName(player) {
                return player.surname.concat(' ', player.name);
            },
            inSelectedPlayers(id) {
                return this.selectedPlayers.includes(id);
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
                this.deselectedGroups.push(group);
            },
            onSubmit() {
                this.submit()
                    .then(response => window.location.href = response)
                    .catch(errors => console.log(errors));
            },
            submit() {
                this.loading = true;
                return new Promise((resolve, reject) => {
                    axios.post('/rounds', {'players': this.selectedPlayers, 'groups': this.selectedGroups})
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

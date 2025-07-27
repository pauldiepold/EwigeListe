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
                   @keyup.enter="enterPressed"
                   type="text"
                   placeholder="Bitte Namen eingeben"/>

            <div class="tw-h-40 tw-scrolling-touch sm:tw-scrolling-auto tw-overflow-auto tw-bg-gray-200 tw-rounded-lg">
                <div class="tw-px-2 tw-py-1 tw-mx-1 tw-my-2 tw-cursor-pointer tw-flex tw-items-center tw-justify-start"
                     v-for="(player) in filteredPlayers"
                     @click="addPlayer(player)">
                    <avatar :path="player.avatar_path" width="6" class="tw-mr-2"></avatar>
                    <div>{{ player.surname.concat(' ', player.name) }}</div>
                </div>
                <div class="tw-px-3 tw-py-2 text-left" v-if="filteredPlayers.length===0">
                    Spieler wurde nicht gefunden.
                </div>
            </div>
        </div>

        <div class="tw-text-center tw-mt-3">
            <a href="/register/quick" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-user-plus tw-mr-1"></i>
                Neuen Spieler registrieren
            </a>
        </div>


        <h5 class="mt-4" v-if="players.length !== 0">
            {{ players.length }} Spieler:<br>
            <span class="tw-text-xs">(Ändern der Reihenfolge durch Ziehen)</span>
        </h5>

        <sortable-players-list lockAxis="y"
                               :useDragHandle="true"
                               v-model:list="players">
            <sortable-player v-for="(player, index) in players"
                             :key="player.id"
                             :player="player"
                             :index="index"
                             :logged-in-player-id="loggedInPlayerId"
                             @remove-player="removePlayer"/>
        </sortable-players-list>

        <div class="d-flex justify-content-center align-items-center tw-my-6">
            <div class="no-underline tw-font-bold tw-text-lg tw-text-gray-800 tw-cursor-pointer"
               @click="liveGame = false">
                Offline
            </div>
            <div class="mx-2">
                <i class="fas fa-toggle-on tw-text-gray-700 tw-text-4xl tw-cursor-pointer"
                   v-if="liveGame"
                   @click="liveGame = !liveGame"/>
                <i class="fas fa-toggle-on tw-text-gray-700 tw-text-4xl fa-flip-horizontal tw-cursor-pointer"
                   v-if="!liveGame"
                   @click="liveGame = !liveGame"/>
            </div>
            <div class="tw-font-bold tw-text-lg no-underline tw-text-gray-800 tw-cursor-pointer"
               @click="liveGame = true">
                Online
            </div>
        </div>

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
                {{ group.name }}
            </span>
            <i class="fas fa-2x mx-1 tw-text-gray-700"
               :class="{'fa-toggle-on': inSelectedGroups(group), 'fa-toggle-off': !inSelectedGroups(group)}"
               v-show="group.id !== 1">
            </i>
        </div>

    </div>
</template>

<script>

export default {
    props: {
        allPlayers: {
            type: Array,
            default() {
                return [];
            },
            required: true,
        },
        loggedInPlayerId: {
            type: Number,
            required: true
        }
    },

    data() {
        return {
            textSearch: '',
            loading: false,
            groups: [1],
            players: [],
            liveGame: false
        }
    },

    created() {
        let self = this;
        let player = self.allPlayers.filter(player => player.id === self.loggedInPlayerId)
        this.addPlayer(player[0]);
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
                    if (!output.map(v => v.id).includes(group.id) && group.closed !== 1) {
                        output.push(group);
                    }
                });
            });

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
        defaultGroups() {
            let output = [];

            this.players.forEach(function (player) {
                player.profiles.forEach(function (profile) {
                    if (profile.default) {
                        if (!output.includes(profile.group_id)) {
                            output.push(profile.group_id)
                        }
                    }
                });
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

    methods: {
        enterPressed() {
            if (this.filteredPlayers.length === 1) {
                this.addPlayer(this.filteredPlayers[0]);
            }
        },
        scrollTo(element) {
            this.$scrollTo(element);
        },
        fullName(player) {
            return player.surname.concat(' ', player.name);
        },
        inSelectedPlayers(player) {
            return this.players.map(o => o.id).includes(player.id);
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

                let self = this;
                player.profiles.forEach(function (profile) {
                    if (profile.default) {
                        if (!self.groups.includes(profile.group_id)) {
                            self.groups.push(profile.group_id)
                        }
                    }
                });
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
                'groups': this.selectedGroups,
                'liveGame': this.liveGame
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

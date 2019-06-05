<template>
    <div class="form-autocomplete">
        <div class="bg-white rounded shadow-2 mx-auto my-4 p-3" style="max-width:19rem;" v-if="players.length < 7">
            <input class="custom-input" :value="textSearch" @input="textSearch = $event.target.value" type="text"
                   :placeholder="placeholder" @focus="show = true" @blur="show = true"/>

            <div class="players-list" v-show="show">
                <div class="player font-weight-bold mt-3 mx-1 text-left" v-for="(player, index) in filteredOptions"
                     @click="addPlayer(player)">
                    {{player.name}}
                </div>
                <p v-if="filteredOptions.length===0">Spieler wurde nicht gefunden</p>
            </div>
        </div>


        <h4 class="mt-4" v-if="players.length !== 0">Ausgewählte Spieler:</h4>
        <div class="players-selected">
            <transition-group name="flip-list">
                <div class="player-selected rounded bg-white px-3 py-2 my-3 mx-auto d-flex align-items-center justify-content-between shadow-2"
                     style="max-width: 19rem;"
                     v-for="(player, key) in players" v-bind:key="player.id">
                <span class="font-weight-bold">
                {{player.name}}
                </span>
                    <span style="font-size: 1.1rem;">
                    <i class="fas fa-chevron-up mx-1 text-muted" @click="moveUp(player)" v-if="key !== 0"></i>
                    <i class="fas fa-chevron-down mx-1 text-muted" @click="moveDown(player)"
                       v-if="key !== players.length-1"></i>
                    <i class="fas fa-trash mx-1 text-danger" @click="removePlayer(key)"></i>
                </span>
                </div>
            </transition-group>
        </div>

        <button class="btn btn-primary mt-4" :disabled="players.length < 4">Neue Runde starten</button>
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
                show: false,
                players: [],
            }
        },
        computed: {
            filteredOptions() {
                return this.allPlayers.filter(player => player.name.toLowerCase().includes(this.textSearch.toLowerCase()) && !this.inSelectedPlayers(player.id)).slice(0, 5);
            },
            selectedPlayers() {
                return this.players.map(v => v.id);
            },

        },
        methods: {
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
            showOptions() {
                this.show = true;
            },

            addPlayer(player) {
                if (!this.inSelectedPlayers(player.id)) {
                    this.textSearch = '';
                    this.show = false;
                    this.players.push(player);
                }
            },

            removePlayer(index) {
                this.textSearch = '';
                this.players.splice(index, 1);
            }
        }
    };
</script>

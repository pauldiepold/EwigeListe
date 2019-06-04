<template>
    <div class="form-autocomplete">
        <div class="form-group">
            <input class="form-control mx-auto" style="max-width: 15rem;" v-model="textSearch" type="text"
                   :placeholder="placeholder" @focus="showOptions"/>
        </div>

        <div class="players-list" v-show="show">
            <div class="player" v-for="(player, index) in filteredOptions" @click="addPlayer(player)">
                {{player.name}}
            </div>
            <p v-if="filteredOptions.length===0">Spieler wurde nicht gefunden</p>
        </div>

        <hr>
        <p class="font-weight-bold">Ausgewählte Spieler:</p>
        <div class="players-selected">
            <div class="player-selected" v-for="(player, key) in players">
                {{player.name}} <button class="btn btn-link" @click="removePlayer(key)">Entfernen</button>
            </div>
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
                return this.allPlayers.filter(player => player.name.toLowerCase().includes(this.textSearch.toLowerCase()) && !this.inSelectedPlayers(player.id)).slice(0, 7);
            },
            selectedPlayers() {
                return this.players.map(v => v.id);
            },

        },
        methods: {
            inSelectedPlayers(id) {
                return this.selectedPlayers.includes(id);
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

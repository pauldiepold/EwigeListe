<template>
    <div>
        <tabs>
            <tab name="Runde" icon="fa-play-circle" :selected="true">
                <create-game :round="round" @updated="fetchData"/>
                <round-table :round="round"/>
                <delete-game :round="round" @updated="fetchData(); deleteLastGame();"/>
                <round-info :round="round"/>
            </tab>

            <tab v-if="round.live_round !== null" name="Live" icon="fa-dice" :selected="false">
                <div id="fullscreen" class="tw-w-1/2 tw-h-1/2 tw-bg-gray-500 tw-mx-auto">
                    {{ orientation }}
                    <div v-if="!fullscreen">
                        <i class="fas fa-expand tw-text-4xl" @click="toggleFullscreen"></i>
                    </div>
                    <div v-else>
                        <i class="fas fa-compress tw-text-4xl" @click="toggleFullscreen"></i>
                        <br>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. A aspernatur autem consequatur
                        deleniti
                        eligendi ex nam quas ratione totam vero! Aliquid asperiores est libero maxime, quasi rerum
                        sapiente
                        voluptas voluptatibus!
                    </div>

                </div>
            </tab>

            <tab v-if="round.games.length >= 4" name="Statistiken" icon="fa-chart-area" :selected="false">
                <template v-slot:default="props">
                    <round-graph :round_id="round.id" :key="props.tabKey"></round-graph>
                </template>
            </tab>

            <tab name="Listen" icon="fa-list-alt" :selected="false">
                <template v-slot:default="props">
                    <update-groups :round-input="round"
                                   :can-update="canUpdate"
                                   :key="props.tabKey"/>
                </template>
            </tab>
        </tabs>
    </div>
</template>

<script>
//import Fullscreen from "vue-fullscreen/src/component.vue"

export default {
    components: {},
    props: {
        roundProp: Object,
        canUpdate: Boolean,
    },
    data() {
        return {
            round: this.roundProp,
            orientation: 0,
            fullscreen: false,
        }
    },
    mounted() {
    },
    created() {
        this.getOrientation();
        this.getFullscreen();
        window.addEventListener("resize", this.getOrientation);
        window.addEventListener("fullscreenchange", this.getFullscreen);
        this.presenceChannel
            .here(players => {
                this.round.online_players = this.pluck(players, 'id');
            })
            .joining(player => {
                this.round.online_players.push(player.id);
            })
            .leaving(player => {
                this.round.online_players.splice(this.round.online_players.indexOf(player.id), 1);
            })
            .listen('RoundUpdated', e => {
                this.fetchData();
            });
    },
    destroyed() {
        window.removeEventListener("resize", this.getOrientation);
        window.removeEventListener("fullscreenchange", this.getFullscreen);
    },
    computed: {
        presenceChannel() {
            return window.Echo
                .join('round.' + this.round.id);
        },
        playersOnline() {
            return this.pluck(this.round.active_players, 'id')
                .every(id => this.round.online_players.includes(id));
        },
    },
    methods: {
        reconnectChannels() {
            this.round.online_players = this.pluck(Object.values(this.presenceChannel.subscription.members.members), 'id');
        },
        fetchData() {
            axios.get('/api/rounds/' + this.roundProp.id + '/fetchData')
                .then(response => {
                    this.round = response.data.data;
                    this.reconnectChannels();
                });
        },
        toggleFullscreen() {
            if (this.fullscreenElement()) {
                document.exitFullscreen();
            } else {
                let elem = document.getElementById('fullscreen');
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            }
        },
        fullscreenElement() {
            return document.fullscreenElement
                || document.webkitFullscreenElement
                || document.mozFullscreenElement
                || document.msFullscreenElement;
        },
        getFullscreen() {
            let fullscreen = !!document.webkitIsFullScreen;
            this.fullscreen = fullscreen;
            return fullscreen;
        },
        getOrientation() {
            let orientation = window.innerWidth > window.innerHeight ? 'Landscape' : 'Portait';
            this.orientation = orientation;
            return orientation;
        },
        deleteLastGame() {
            this.round.games.splice(this.round.games.indexOf(this.round.games.length - 1), 1);
        },
        pluck(array, key) {
            return array.map(o => o[key]);
        },
    },
};
</script>

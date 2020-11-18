<template>
    <div>
        <tabs @clicked="fullscreenIfMobile">
            <tab name="Runde" icon="fa-play-circle" :selected="true">
                <create-game :round="round" @updated="fetchData"/>
                <round-table :round="round"/>
                <delete-game :round="round" @updated="fetchData(); deleteLastGame();"/>
                <round-info :round="round"/>
            </tab>

            <tab name="Virus" icon="fa-basketball-ball">

            </tab>

            <tab v-if="round.live_round !== null" name="Live" icon="fa-dice" :selected="false" @clicked="alert('test')">
                <div id="fullscreen" class="tw-w-100 tw-relative tw-pt-50p">
                    <div
                        class="tw-absolute tw-bottom-0 tw-top-0 tw-left-0 tw-right-0 tw-bg-gray-400"
                        :class="{'tw-rounded-xl tw-shadow-xl': !fullscreen}"
                        style="background-image: url('/img/wood.jpg');">
                        <div class="tw-relative tw-h-full tw-w-full">

                            <div v-if="mobile && !fullscreen"
                                 class="tw-text-gray-200 tw-bg-gray-800 tw-bg-opacity-50 tw-p-2 tw-w-48 tw-mx-auto tw-rounded-xl tw-mt-12">
                                <p>
                                    Bitte aktiviere den Fullscreen-Modus!
                                </p>
                                <i class="fas fa-expand tw-text-4xl" @click="fullscreenOn"></i>
                            </div>
                            <div v-if="mobile && !landscape && fullscreen"
                                 class="tw-text-gray-200 tw-bg-gray-800 tw-bg-opacity-50 tw-p-2 tw-w-48 tw-mx-auto tw-rounded-xl tw-mt-16">
                                Bitte drehe dein Gerät in den Landscape Modus!
                            </div>
                            <div v-show="!mobile || (landscape && fullscreen)">
                                <!--<live-game :round="round"/>-->
                                <i v-if="mobile && fullscreen"
                                   class="tw-absolute tw-ml-2 tw-mt-2 tw-top-0 tw-left-0 fas fa-compress tw-text-4xl tw-text-gray-600"
                                   @click="fullscreenOff"></i>

                            </div>
                        </div>
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
            landscape: false,
            fullscreen: false,
            mobile: false,
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
        fullscreenOn() {
            let elem = document.getElementById('fullscreen');
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        },
        fullscreenOff() {
            document.exitFullscreen();
        },
        fullscreenIfMobile() {
            if (this.mobile && !this.fullscreen) {
                this.fullscreenOn();
            }
        },
        getFullscreen() {
            let fullscreen = !!document.webkitIsFullScreen;
            this.fullscreen = fullscreen;
            return fullscreen;
        },
        getOrientation() {
            this.landscape = window.innerWidth > window.innerHeight;
            this.mobile = window.screen.width <= 900;
            if (!this.mobile && this.fullscreen) {
                this.fullscreenOff();
            }
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

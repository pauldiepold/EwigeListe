<template>
    <div>
        <tabs>
            <tab name="Runde" icon="fa-play-circle" :selected="true">
                <create-game :round="round" @updated="fetchData"/>
                <round-table :round="round"/>
                <delete-game :round="round" @updated="fetchData(); deleteLastGame();"/>
                <round-info :round="round"/>
            </tab>

            <tab v-if="false && round.live_round !== null" name="Live" icon="fa-dice" :selected="false">

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
export default {
    props: {
        roundProp: Object,
        canUpdate: Boolean,
    },
    data() {
        return {
            round: this.roundProp,
        }
    },
    created() {
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
        deleteLastGame() {
            this.round.games.splice(this.round.games.indexOf(this.round.games.length - 1), 1);
        },
        pluck(array, key) {
            return array.map(o => o[key]);
        },
    },
};
</script>

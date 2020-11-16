<template>
    <div>
        <tabs>
            <tab name="Runde" icon="fa-play-circle" :selected="true">
                <create-game :round="round" @updated="fetchData"/>
                <round-table :round="round"/>
                <round-info :round="round"/>
            </tab>

            <tab v-if="round.live_round !== null" name="Live" icon="fa-dice" :selected="false">

            </tab>

            <tab v-show="round.games.length >= 4" name="Statistiken" icon="fa-chart-area" :selected="false">
                <template v-slot:default="props">
                    <!--<round-graph :round_id="round.id" :key="props.tabKey"></round-graph>-->
                </template>
            </tab>

            <tab name="Listen" icon="fa-list-alt">
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
        authId: Number,
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
                alert('roundupdated');
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
        pluck(array, key) {
            return array.map(o => o[key]);
        },
    },
};
</script>

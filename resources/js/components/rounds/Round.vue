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

            <tab v-if="round.games.length >= 4" name="Statistiken" icon="fa-chart-area" :selected="false">
                <template v-slot:default="props">
                    <round-graph :round_id="round.id" :key="props.tabKey"></round-graph>
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
    },
    data() {
        return {
            round: this.roundProp,
        }
    },
    methods: {
        fetchData() {
            axios.get('/api/rounds/' + this.roundProp.id + '/fetchData')
                .then(response => {
                    this.round = response.data.data;
                });
        },
    },
};
</script>

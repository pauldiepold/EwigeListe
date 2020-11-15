<template>
    <div>
        <tabs>
            <tab name="Runde" icon="fa-play-circle" :selected="true">

            </tab>

            <tab name="Live" icon="fa-dice" :selected="false">

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
                                   :key="props.tabKey" />
                </template>
            </tab>

            <tab name="Kommentare" icon="fa-comments">

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
            round: '',
        }
    },
    mounted() {
        this.fetchData();
    },
    methods: {
        fetchData() {
            axios.get('/api/rounds/' + this.roundProp.id + '/fetchData')
                .then(response => {
                    this.round = response.data.data;
                    /*this.ich = response.data.ich;
                    this.liveGame = response.data.liveGame;

                    this.ich.hand = Object.values(this.ich.hand);
                    this.ich.moeglicheVorbehalte = Object.values(this.ich.moeglicheVorbehalte);

                    this.liveGame.spielerIDs.forEach(spielerID => {
                        if (!this.players.includes(spielerID)) {
                            this.liveGame.spieler[spielerID].online = false;
                        }
                    });*/
                });
        },
    },
};
</script>

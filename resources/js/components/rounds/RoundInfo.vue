<template>
    <div class="text-muted tw-text-sm tw-mt-6" style="font-size:0.88rem;">
        <div v-if="round.games.length !== 0">
            <p class="mb-2">
                Anzahl Spiele: {{ round.games.length }}
            </p>

            <div v-if="round.games.length >= 4">
                <p>
                    Durchschnittliche Spieldauer: {{ gameLength }}
                </p>
            </div>
        </div>
        <p class="mb-2 mt-3">
            Runde gestartet von {{ round.created_by.surname }}
            <br class="d-block d-sm-none"> {{ round.created_at_print }}.
        </p>
        <p v-if="round.games.length >= 1">
            Letztes Spiel eingetragen von {{ this.round.games[this.round.games.length - 1].created_by.surname }}
            <br class="d-block d-sm-none"> {{ this.round.games[this.round.games.length - 1].created_at_print }}.
        </p>
    </div>
</template>

<script>
import moment from 'moment';

moment.locale('de');

export default {
    props: ['round'],
    data() {
        return {}
    },
    created() {

    },
    computed: {
        beginOfRound() {
            return moment(this.round.created_at)
        },
        lastGame() {
            return moment(this.round.games[this.round.games.length - 1].created_at);
        },
        firstGame() {
            return moment(this.round.games[0].created_at);
        },
        gameLengthSeconds() {
            if (false && moment().diff(this.lastGame, 'seconds') < 60 * 30) {
                return moment().diff(this.firstGame, 'seconds') / (this.round.games.length);
            } else {
                return this.lastGame.diff(this.firstGame, 'seconds') / (this.round.games.length - 1);
            }
        },
        gameLength() {
            return Math.floor(this.gameLengthSeconds / 60) + ' Minuten und ' + Math.round(this.gameLengthSeconds % 60) + ' Sekunden';
        },
    },
    methods: {
        pluck(array, key) {
            return array.map(o => o[key]);
        },
    }
};
</script>

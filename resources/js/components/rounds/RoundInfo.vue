<template>
    <div class="text-muted tw-text-sm tw-mt-6" style="font-size:0.88rem;">
        <div v-if="round.games.length !== 0">
            <p class="mb-2">
                Anzahl Spiele: {{ round.games.length }}
            </p>
            <!--
            <div v-if="round.games.length >= 4">
                @php
                $gameLengthSeconds =
                $gameLength = floor($gameLengthSeconds / 60) . ' Minuten und ' . ($gameLengthSeconds % 60) . '
                Sekunden';
                @endphp

                @if( ($gameLengthSeconds / 60 >= 0) && ($gameLengthSeconds / 60 <= 25))
                <p>
                    Durchschnittliche Spieldauer: {{ $gameLength }}
                </p>
                @endif
                @endif
            </div>
-->
            <p class="mb-2 mt-3">
                Runde gestartet von {{ round.created_by.surname }}
                <br class="d-block d-sm-none"> {{ round.created_at }}.
            </p>
            <p v-if="round.games.length >= 1">
                Letztes Spiel eingetragen von {{ lastGame.created_by.surname }}
                <br class="d-block d-sm-none"> {{ lastGame.created_at }}.
            </p>
        </div>
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
    computed: {
        lastGame() {
            return this.round.games[this.round.games.length - 1];
        },
        firstGame() {
            return this.round.games[0];
        },
        gameLength() {
            return moment(this.lastGame.created_at).diff(moment(this.firstGame.created_at));
        },
    },
    methods: {
        pluck(array, key) {
            return array.map(o => o[key]);
        },
    }
};
</script>

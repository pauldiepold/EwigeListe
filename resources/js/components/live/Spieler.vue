<template>
    <div>
        <div style="position: absolute; left: 15%; bottom: 0; transform: translate(-50%);"
             class="tw-bg-gray-800 tw-bg-opacity-50 tw-rounded-t-lg tw-text-gray-300 tw-px-3 tw-py-1 tw-text-sm tw-z-50 tw-flex tw-flex-col"
             :class="{'tw-shadow-green-darker': liveGame.dran === round.active_players[0].id}">
            <div style="white-space: nowrap">
                <span v-if="liveGame.phase === 2 && round.active_players[0].id === liveGame.vorhand">Vorhand</span>
                <span
                    v-if="(liveGame.phase === 2 && round.active_players[0].id === liveGame.vorhand) && getInfo(0, 'vorbehalt')">
                    |
                </span>
                {{ getInfo(0, 'vorbehalt') }}
                <br v-if="(liveGame.phase === 2 && round.active_players[0].id === liveGame.vorhand) || getInfo(0, 'vorbehalt')">
                {{ getInfo(0, 'partei') }}
                <span
                    v-if="getInfo(0, 'ansage') && !(getInfo(0, 'ansage') === 'Kontra' && liveGame.spieltyp === 'Hochzeit' && getInfo(0, 'partei') === '')">
                    |
                </span>
                {{ getInfo(0, 'ansage') }}
                <br v-if="getInfo(0, 'partei') || (getInfo(0, 'ansage') === 'Kontra' && liveGame.spieltyp === 'Hochzeit')">
                <b>{{ round.active_players[0].surname }}</b>
                <span v-if="getInfo(0, 'spieltyp')">|</span>
                {{ getInfo(0, 'spieltyp') }}
            </div>
        </div>
        <div style="position: absolute; left: 20%; transform: translate(-50%);"
             class="tw-bg-gray-800 tw-bg-opacity-50 tw-rounded-b-lg tw-text-gray-300 tw-px-3 tw-py-1 tw-text-sm"
             :class="{'tw-shadow-green-darker': liveGame.dran === round.active_players[1].id}">
            <div style="white-space: nowrap">
                <b>{{ round.active_players[1].surname }}</b>
                <span v-if="getInfo(1, 'spieltyp')">|</span>
                {{ getInfo(1, 'spieltyp') }}
                <br v-if="getInfo(1, 'partei') || (getInfo(1, 'ansage') === 'Kontra' && liveGame.spieltyp === 'Hochzeit')">
                {{ getInfo(1, 'partei') }}
                <span
                    v-if="getInfo(1, 'ansage') && !(getInfo(1, 'ansage') === 'Kontra' && liveGame.spieltyp === 'Hochzeit' && getInfo(1, 'partei') === '')">
                    |
                </span>
                {{ getInfo(1, 'ansage') }}
                <br v-if="(liveGame.phase === 2 && round.active_players[1].id === liveGame.vorhand) || getInfo(1, 'vorbehalt')">
                <span v-if="liveGame.phase === 2 && round.active_players[1].id === liveGame.vorhand">Vorhand</span>
                <span
                    v-if="(liveGame.phase === 2 && round.active_players[1].id === liveGame.vorhand) && getInfo(1, 'vorbehalt')">
                    |
                </span>
                {{ getInfo(1, 'vorbehalt') }}
            </div>
        </div>
        <div style="position: absolute; left: 50%; transform: translate(-50%);"
             class="tw-bg-gray-800 tw-bg-opacity-50 tw-rounded-b-lg tw-text-gray-300 tw-px-3 tw-py-1 tw-text-sm"
             :class="{'tw-shadow-green-darker': liveGame.dran === round.active_players[2].id}">
            <div style="white-space: nowrap">
                <b>{{ round.active_players[2].surname }}</b>
                <span v-if="getInfo(2, 'spieltyp')"> |</span>
                {{ getInfo(2, 'spieltyp') }}
                <br
                    v-if="getInfo(2, 'partei') || (getInfo(2, 'ansage') === 'Kontra' && liveGame.spieltyp === 'Hochzeit')">

                {{ getInfo(2, 'partei') }}
                <span
                    v-if="getInfo(2, 'ansage') && !(getInfo(2, 'ansage') === 'Kontra' && liveGame.spieltyp === 'Hochzeit' && getInfo(2, 'partei') === '')">
                    |
                </span>
                {{ getInfo(2, 'ansage') }}
                <br v-if="(liveGame.phase === 2 && round.active_players[2].id === liveGame.vorhand) || getInfo(2, 'vorbehalt')">
                <span v-if="liveGame.phase === 2 && round.active_players[2].id === liveGame.vorhand">Vorhand</span>
                <span
                    v-if="(liveGame.phase === 2 && round.active_players[2].id === liveGame.vorhand) && getInfo(2, 'vorbehalt')">
                    |
                </span>
                {{ getInfo(2, 'vorbehalt') }}
            </div>
        </div>
        <div style="position: absolute; left: 80%; transform: translate(-50%);"
             class="tw-bg-gray-800 tw-bg-opacity-50 tw-rounded-b-lg tw-text-gray-300 tw-px-3 tw-py-1 tw-text-sm"
             :class="{'tw-shadow-green-darker': liveGame.dran === round.active_players[3].id}">
            <div style="white-space: nowrap">
                <b>{{ round.active_players[3].surname }}</b>
                <span v-if="getInfo(3, 'spieltyp')"> |</span>
                {{ getInfo(3, 'spieltyp') }}
                <span
                    v-if="getInfo(3, 'partei') || (getInfo(3, 'ansage') === 'Kontra' && liveGame.spieltyp === 'Hochzeit')">
                    <br>
                </span>
                {{ getInfo(3, 'partei') }}
                <span
                    v-if="getInfo(3, 'ansage') && !(getInfo(3, 'ansage') === 'Kontra' && liveGame.spieltyp === 'Hochzeit' && getInfo(3, 'partei') === '')">
                    |
                </span>
                {{ getInfo(3, 'ansage') }}
                <br v-if="(liveGame.phase === 2 && round.active_players[3].id === liveGame.vorhand) || getInfo(3, 'vorbehalt')">
                <span v-if="liveGame.phase === 2 && round.active_players[3].id === liveGame.vorhand">Vorhand</span>
                <span
                    v-if="(liveGame.phase === 2 && round.active_players[3].id === liveGame.vorhand) && getInfo(3, 'vorbehalt')">
                    |
                </span>
                {{ getInfo(3, 'vorbehalt') }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        round: Object,
        liveGame: Object,
    },

    data() {
        return {}
    },

    created() {
    },

    computed: {},
    methods: {
        getInfo(position, value) {
            return this.liveGame.spieler[this.round.active_players[position].id][value];
        }
    }
};
</script>

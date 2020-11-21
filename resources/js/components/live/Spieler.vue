<template>
    <!--<div :class="{ 'tw-border-green-400' : dran && true/*spieler.online*/, 'tw-border-red-400' : false /*!spieler.online*/}"
         style="margin: 1px;"
         class="tw-flex tw-items-center tw-rounded-lg tw-border-2 tw-border-gray-500 tw-p-2">
        <div class="tw-flex tw-items-center tw-pr-2 tw-py-1 tw-pl-1">
            <img :src="spieler.avatar"
                 :class="{ 'grayscale': false /*!spieler.online*/ }"
                 class="tw-h-20 tw-w-20 tw-rounded-full">
        </div>
        <div class="tw-flex tw-flex-col tw-justify-around tw-px-2 tw-border-r-2 tw-border-l-2 tw-border-gray-500">
            <div class="tw-font-bold tw-text-lg">{{ spieler.surname }}</div>
            <div>Punkte: {{ spieler.punkte}}</div>
            <div v-if="letzterStich">
                <slot name="letzterStich"></slot>
            </div>
        </div>
        <div v-if="spieler.ansage"
             class="tw-font-bold tw-flex tw-flex-col tw-justify-around tw-px-2 tw-border-r-2 tw-border-gray-500">
            <div v-text="spieler.ansage" v-if="spieler.ansage"></div>
            <div v-text="spieler.absage" v-if="spieler.absage"></div>
        </div>
        <div v-if="hasSlotAnsagen" class="tw-flex tw-flex-col tw-items-center tw-justify-center tw-pl-2">
            <slot name="ansagen"></slot>
        </div>
    </div>-->
    <div>
        <div style="position: absolute; left: 25%; transform: translate(-50%);"
             class="tw-bg-gray-800 tw-bg-opacity-50 tw-rounded-b-lg tw-text-gray-300 tw-text tw-px-4"
             :class="{'tw-shadow-green-darker': liveGame.dran === round.active_players[1].id}">
            {{ round.active_players[1].surname }}
        </div>
        <div style="position: absolute; left: 50%; transform: translate(-50%);"
             class="tw-bg-gray-800 tw-bg-opacity-50 tw-rounded-b-lg tw-text-gray-300 tw-text tw-px-4"
             :class="{'tw-shadow-green-darker': liveGame.dran === round.active_players[2].id}">
            {{ round.active_players[2].surname }}
        </div>
        <div style="position: absolute; left: 75%; transform: translate(-50%);"
             class="tw-bg-gray-800 tw-bg-opacity-50 tw-rounded-b-lg tw-text-gray-300 tw-text tw-px-4"
             :class="{'tw-shadow-green-darker': liveGame.dran === round.active_players[3].id}">
            {{ round.active_players[3].surname }}
        </div>
        <div style="position: absolute; left: 20%; bottom: 0; transform: translate(-50%);"
             class="tw-bg-gray-800 tw-bg-opacity-50 tw-rounded-t-lg tw-text-gray-300 tw-text tw-px-4 tw-z-50"
             :class="{'tw-shadow-green-darker': liveGame.dran === round.active_players[0].id}">
            {{ round.active_players[0].surname }}
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

    computed: {/*
        dran() {
            return this.liveGame.dran === this.spieler.id;
        },

        letzterStich() {
            return this.liveGame.letzterStich.stecher === this.spieler.id;
        },

        hasSlotAnsagen() {
            return !!this.$slots.ansagen;
        }*/
    },
};
</script>

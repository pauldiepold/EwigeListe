<template>
    <div v-if="karte === 'unsichtbar'" class="tw-invisible">
        *
    </div>
    <div v-else-if="armut"
         @click="$emit('armut', karte)">
        <img class="card" :src="'/cards/' + filename">
    </div>
    <!--         class="card-live card-clickable tw-cursor-pointer"-->
    <div v-else-if="karte.spielbar"
         @click="$emit('karteSpielen', karte)">
        <img class="card" :src="'/cards/' + filename">
    </div>
    <div v-else-if="!karte.spielbar">
        <img class="card" :src="'/cards/' + filename">
    </div>


</template>

<script>
export default {
    props: {
        karte: [Object, String],
        armut: {
            type: Boolean,
            required: false,
            default: false,
        },
        keinRoterRahmen: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    data: function () {
        return {
            id: 0
        }
    },

    created() {
        this.id = this.karte.id;
    },

    computed: {
        wert() {
            if (this.karte.wert === 1) {
                return '9';
            } else if (this.karte.wert === 2) {
                return 'J';
            } else if (this.karte.wert === 3) {
                return 'Q';
            } else if (this.karte.wert === 4) {
                return 'K';
            } else if (this.karte.wert === 5) {
                return '10';
            } else if (this.karte.wert === 6) {
                return 'A';
            } else {
                return '';
            }
        },
        farbe() {
            if (this.karte.farbe === 1) {
                return 'D';
            } else if (this.karte.farbe === 2) {
                return 'H';
            } else if (this.karte.farbe === 3) {
                return 'S';
            } else if (this.karte.farbe === 4) {
                return 'C';
            } else {
                return '';
            }
        },
        filename() {
            return this.wert + this.farbe + '.svg';
        }
    },
};
</script>

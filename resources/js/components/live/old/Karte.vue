<template>
    <div v-if="karte === 'unsichtbar'"
         class="card-live back tw-invisible">
        *
    </div>
    <a v-else-if="armut"
       class="card-live"
       href="#"
       :class="[wert_klasse, farbe]"
       @click="$emit('armut', karte)">
        <span class="rank" v-html="wert"></span>
        <span class="suit" v-html="'&' + farbe + ';'"></span>
    </a>
    <a v-else-if="karte.spielbar"
       class="card-live"
       href="#"
       :class="[wert_klasse, farbe]"
       @click="$emit('karteSpielen', karte)">
        <span class="rank" v-html="wert"></span>
        <span class="suit" v-html="'&' + farbe + ';'"></span>
    </a>
    <div v-else-if="!karte.spielbar"
         class="card-live"
         :class="[wert_klasse, farbe, !keinRoterRahmen ? 'karteNichtSpielbar' : '']">
        <span class="rank" v-html="wert"></span>
        <span class="suit" v-html="'&' + farbe + ';'"></span>
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
            wert_klasse() {
                if (this.karte.wert === 1) {
                    return 'rank-9';
                } else if (this.karte.wert === 2) {
                    return 'rank-j';
                } else if (this.karte.wert === 3) {
                    return 'rank-q';
                } else if (this.karte.wert === 4) {
                    return 'rank-k';
                } else if (this.karte.wert === 5) {
                    return 'rank-10';
                } else if (this.karte.wert === 6) {
                    return 'rank-a';
                } else {
                    return '';
                }
            },
            farbe() {
                if (this.karte.farbe === 1) {
                    return 'diams';
                } else if (this.karte.farbe === 2) {
                    return 'hearts';
                } else if (this.karte.farbe === 3) {
                    return 'spades';
                } else if (this.karte.farbe === 4) {
                    return 'clubs';
                } else {
                    return '';
                }
            },
        },
    };
</script>

<template>
    <div class="tw-max-w-xs tw-mx-auto tw-mb-8">
        <div class="tw-flex tw-justify-center tw--mb-8">
            <karte :karte="karten[2]"/>
        </div>
        <div class="tw-flex tw-justify-between">
            <karte :karte="karten[1]"/>
            <karte :karte="karten[3]"/>
        </div>
        <div class="tw-flex tw-justify-center tw--mt-8">
            <karte :karte="karten[0]"/>
        </div>
    </div>
</template>

<script>
    import Karte from "./Karte";

    export default {
        components: {
            Karte
        },

        props: {
            stich: Array,
            authId: Number,
            spielerIds: Array
        },

        data() {
            return {}
        },

        computed: {
            karten() {
                let karten = ['unsichtbar', 'unsichtbar', 'unsichtbar', 'unsichtbar'];

                this.stich.forEach(karte => {
                    let position = this.positionVonSpieler(karte.gespieltVon);
                    karten[position] = karte;
                });

                return karten;
            }
        },

        methods: {
            positionVonSpieler(spielerID) {
                let eigenerIndex = this.spielerIds.indexOf(this.authId);
                let fremderIndex = this.spielerIds.indexOf(spielerID);

                let ergebnis = fremderIndex - eigenerIndex;
                if (ergebnis < 0) {
                    ergebnis += 4;
                }
                return ergebnis;
            }
        }
    };
</script>

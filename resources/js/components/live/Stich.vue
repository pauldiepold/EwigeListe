<template>
    <div class="max-w-xs mx-auto">
        <div class="flex justify-center -mb-12">
            <karte style="margin: 0 !important;" :karte="karten[2]"/>
        </div>
        <div class="flex justify-between">
            <div class="mr-16">
                <karte style="margin: 0 !important;" :karte="karten[1]"/>
            </div>
            <div class="ml-16">
                <karte class="rotate-90" style="margin: 0 !important;" :karte="karten[3]"/>
            </div>
        </div>
        <div class="flex justify-center -mt-12">
            <karte style="margin: 0 !important;" :karte="karten[0]"/>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        stich: Array,
        round: Object,
    },

    data() {
        return {}
    },

    computed: {
        karten() {
            let karten = ['unsichtbar', 'unsichtbar', 'unsichtbar', 'unsichtbar'];

            this.stich.forEach(karte => {
                let position = this.round.active_players.map(v => v.id).indexOf(karte.gespieltVon);
                karten[position] = karte;
            });

            return karten;
        }
    },

    methods: {
    }
};
</script>

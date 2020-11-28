<template>
    <div class="tw-max-w-xs tw-mx-auto">
        <div class="tw-flex tw-justify-center tw--mb-12">
            <karte style="margin: 0 !important;" :karte="karten[2]"/>
        </div>
        <div class="tw-flex tw-justify-between">
            <div class="tw-mr-16">
                <karte style="margin: 0 !important;" :karte="karten[1]"/>
            </div>
            <div class="tw-ml-16">
                <karte class="tw-rotate-90" style="margin: 0 !important;" :karte="karten[3]"/>
            </div>
        </div>
        <div class="tw-flex tw-justify-center tw--mt-12">
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
                let position = this.pluck(this.round.active_players, 'id').indexOf(karte.gespieltVon);
                karten[position] = karte;
            });

            return karten;
        }
    },

    methods: {
    }
};
</script>

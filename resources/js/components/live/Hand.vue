<template>
    <ul class="hand">
        <li v-for="karte in karten" :key="karte.id">
            <karte :karte="karte"
                   :armut="armut"
                   @karteSpielen="$emit('karteSpielen', $event)"
                   @armut="$emit('armut', $event)"/>
        </li>
    </ul>
</template>

<script>
    import Karte from "./Karte";

    export default {
        components: {
            Karte
        },

        props: {
            karten: [Array, Object],
            armut: {
                type: Boolean,
                required: false,
                default: false,
            },
        },

        computed: {},
    };
</script>

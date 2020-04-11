<template>
    <ul class="hand">
        <li v-for="karte in karten" :key="karte.id">
            <karte :karte="karte"
                   @karteSpielen="$emit('karteSpielen', $event)"/>
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
            karten: [Array, Object]
        },

        computed: {},
    };
</script>

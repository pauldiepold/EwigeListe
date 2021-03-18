<template>
    <div class="hand fan active-hand " data-fan="spacing: 0.5; radius: 800; width:120">
        <karte v-for="karte in karten"
               :key="karte.id"
               :karte="karte"
               :armut="armut"
               @karteSpielen="$emit('karteSpielen', $event)"
               @armut="$emit('armut', $event)"/>
    </div>
</template>

<script>
export default {
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

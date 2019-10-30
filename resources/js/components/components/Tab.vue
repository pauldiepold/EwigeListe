<template>
    <div v-show="isActive">
        <slot></slot>
    </div>
</template>

<script>
    import RoundGraph from "../RoundGraph";

    export default {
        components: {
            RoundGraph
        },
        props: {
            name: {
                required: true
            },
            icon: {
                required: true
            },
            selected: {
                default: false
            }
        },

        data() {
            return {
                isActive: false
            };
        },

        computed: {
            href() {
                return '#' + this.name.toLowerCase().replace(/ /g, '-');
            }
        },

        mounted() {
            this.isActive = this.selected;
        },
    };
</script>

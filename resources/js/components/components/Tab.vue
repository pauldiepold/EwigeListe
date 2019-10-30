<template>
    <div v-show="isActive">
        <slot :tabKey="tabKey"></slot>
    </div>
</template>

<script>
    export default {
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
                isActive: false,
                tabKey: 0,
                route: window.location.hash,
            };
        },

        computed: {
            href() {
                return '#' + this.name.toLowerCase().replace(/ /g, '-');
            }
        },

        mounted() {
            if (this.route !== '') {
                this.isActive = this.route === this.href;
            } else {
                this.isActive = this.selected;
            }
        },
    };
</script>

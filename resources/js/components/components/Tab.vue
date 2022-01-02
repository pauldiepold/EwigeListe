<template>
    <div v-show="isActive">
        <slot :tabKey="tabKey"></slot>
    </div>
</template>

<script>
    export default {
        name: 'Tab',
        props: {
            name: {
                required: true
            },
            icon: {
                required: true
            },
            selected: {
                default: false
            },
            comments: {
                required: false,
                default: 0
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

        created() {

            this.$parent.tabs.push(this);

        },
    };
</script>

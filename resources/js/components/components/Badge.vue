<template>
    <div
        class="tw-m-4 tw-p-4 tw-w-48 tw-rounded-lg tw-bg-yellow-400 shadow-2
        tw-flex tw-flex-col tw-justify-between">
        <div>
            <i class="fas fa-crown"></i>
            {{ name }}
        </div>
        <div>
            <span class="tw-font-bold">{{ value }}</span>
            {{ this.typeDeutsch }}
        </div>
        <div class="tw-font-sm">
            im {{ date }}
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'date',
            'name',
            'value',
            'type'
        ],

        computed: {
            typeDeutsch() {
                if (this.type === 'points') {
                    return 'Punkte';
                } else if (this.type === 'games') {
                    return 'Spiele';
                }
            }
        },

        data() {
            return {};
        },
    };
</script>

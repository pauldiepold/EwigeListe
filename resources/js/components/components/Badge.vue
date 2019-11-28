<template>
    <a :href="path"
       class="tw-m-4 tw-p-2 tw-mx-auto tw-w-64 tw-rounded-lg tw-bg-yellow-400 shadow-2 tw-cursor-pointer
        tw-flex tw-flex tw-content-center tw-no-underline tw-text-black hover:tw-no-underline hover:tw-text-black">
        <div class="tw-flex-1 tw-flex tw-flex-col">
            <div>
                <i class="fas fa-crown tw-text-lg"></i>
            </div>
            <div class="tw-font-bold tw-text-3xl tw--my-2 font-dancing">
                {{ value }}
            </div>
            <div class="tw-text-xs tw-text-gray-700">
                {{ this.typeDeutsch }}
            </div>
        </div>
        <div
            class="tw-flex-2 tw-flex tw-flex-col tw-content-center tw-justify-center">
            <div class="tw--mx-2 tw-text-3xl font-dancing">
                {{ name }}
            </div>
            <div class="tw-text-xs tw-text-gray-700">
                {{ date }}
            </div>
        </div>
    </a>
</template>

<script>
    export default {
        props: [
            'date',
            'name',
            'value',
            'type',
            'path'
        ],

        computed: {
            typeDeutsch() {
                if (this.type === 'points') {
                    return 'Meiste Punkte';
                } else if (this.type === 'games') {
                    return 'Meiste Spiele';
                }
            }
        },

        data() {
            return {};
        },
    };
</script>

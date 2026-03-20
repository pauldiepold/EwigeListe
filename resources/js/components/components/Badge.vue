<template>
    <a :href="path"
       class="m-4 p-2 mx-auto w-64 rounded-lg bg-yellow-400 shadow-2 cursor-pointer
        flex flex content-center no-underline text-black hover:no-underline hover:text-black">
        <div class="flex-1 flex flex-col">
            <div>
                <i class="fas fa-crown text-lg"></i>
            </div>
            <div class="font-bold text-3xl -my-2 font-dancing">
                {{ value }}
            </div>
            <div class="text-xs text-gray-700">
                {{ this.typeDeutsch }}
            </div>
        </div>
        <div
            class="flex-2 flex flex-col content-center justify-center">
            <div class="-mx-2 text-3xl font-dancing">
                {{ name }}
            </div>
            <div class="text-xs text-gray-700">
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

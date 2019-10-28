<template>
    <div
        class="d-flex align-items-center justify-content-between group">
        <span class="font-weight-bold">
            {{player.surname.concat(' ', player.name)}}
        </span>
        <span style="font-size: 1.1rem;" class="tw-cursor-default">
            <i class="fas fa-trash ml-1 mr-2 text-danger tw-cursor-pointer" @click="$emit('remove-player', player)"></i>
            <i class="fas fa-grip-vertical mx-1 tw-cursor-pointer" v-handle v-show="players.length > 1"></i>
        </span>
    </div>
</template>

<script>
    import {ElementMixin, HandleDirective} from "vue-slicksort";

    export default {
        mixins: [ElementMixin],
        props: [
            'players',
            'player',
            'index',
        ],
        methods: {
        },
        directives: {
            handle: HandleDirective
        }
    };
</script>

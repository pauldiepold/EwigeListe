<template>
    <div class="tw-flex align-items-center justify-content-between group">
        <div v-handle
             class="tw-flex-grow rounded tw--mx-4 tw-px-4 tw--my-2 tw-py-2 font-weight-bold tw-flex tw-justify-start tw-items-center tw-cursor-pointer tw-bg-gray-200">
            <img :src="player.avatar_path"
                 class="tw-mr-3 md:tw-h-10 md:tw-w-10 tw-h-8 tw-w-8 tw-rounded-full">
            {{ player.surname.concat(' ', player.name) }}
        </div>
        <div class="tw-ml-4 rounded tw--mx-4 tw-px-4 tw--my-2 tw-py-2 tw-text-lg tw-cursor-pointer tw-bg-gray-100"
             @click.prevent="handler"
             @click="$emit('remove-player', player)"
             v-show="player.id !== loggedInPlayerId">
            <i class="fas fa-trash text-danger"></i>
        </div>
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
        'loggedInPlayerId'
    ],
    methods: {},
    directives: {
        handle: HandleDirective
    }
};
</script>

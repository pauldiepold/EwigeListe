<template>
    <div class="flex align-items-center justify-content-between group">
        <div v-handle
             class="grow rounded -mx-4 px-4 -my-2 py-2 font-weight-bold flex justify-start items-center cursor-pointer">
            <img :src="player.avatar_path"
                 class="mr-3 md:h-10 md:w-10 h-8 w-8 rounded-full">
            {{ player.surname.concat(' ', player.name) }}
        </div>
        <div class="ml-4 rounded -mx-4 px-4 -my-2 py-2 text-lg cursor-pointer"
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

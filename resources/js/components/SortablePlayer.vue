<template>
    <div
        class="d-flex align-items-center justify-content-between group">
        <div class="font-weight-bold tw-flex tw-justify-start tw-items-center">
            <img :src="player.avatar_path"
                 class="tw-mr-3 md:tw-h-10 md:tw-w-10 tw-h-8 tw-w-8 tw-rounded-full">
            {{player.surname.concat(' ', player.name)}}
        </div>
        <div style="font-size: 1.1rem;" class="tw-cursor-default tw-flex">
            <div class="tw-px-2 tw-cursor-pointer "
                 @click="$emit('remove-player', player)"
                 v-show="player.id !== loggedInPlayerId">
                <i class="fas fa-trash text-danger"></i>
            </div>
            <div class="tw-pl-1 tw-pr-2 tw--mr-2 tw-flex tw-items-center tw-cursor-pointer"
                 v-show="players.length > 1"
                 v-handle>
                <i class="fas fa-grip-vertical"></i>
            </div>
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

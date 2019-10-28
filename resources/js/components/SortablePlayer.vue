<template>
    <div
        class="d-flex align-items-center justify-content-between group">
        <div class="font-weight-bold">
            {{player.surname.concat(' ', player.name)}}
        </div>
        <div style="font-size: 1.1rem;" class="tw-cursor-default tw-flex">
            <div class="tw-px-2 tw-cursor-pointer "
                 @click="$emit('remove-player', player)">
                <i class="fas fa-trash text-danger"></i>
            </div>
            <div class="tw-pl-2 tw-pr-5 tw--mr-4 tw--my-2 tw-flex tw-items-center tw-cursor-pointer"
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
        ],
        methods: {},
        directives: {
            handle: HandleDirective
        }
    };
</script>

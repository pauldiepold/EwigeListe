<template>
    <div>
        <div v-show="show === 0">
            <p class="tw-my-6">Möchstest du einen</p>
            <button @click="show = 1"
                    v-show="show === 0"
                    class="btn btn-outline-primary">
                <div class="tw-flex tw-items-center tw-font-bold">
                    <i class="fas fa-user-plus tw-text-2xl tw-mr-2 tw-text-black"></i>
                    <span>Neuen Account</span>
                </div>
            </button>

            <p class="tw-my-4">oder</p>

            <button @click="show = 2"
                    v-show="show === 0"
                    class="btn btn-outline-primary">
                <div class="tw-flex tw-items-center tw-font-bold">
                    <i class="fas fa-user tw-text-2xl tw-mr-2 tw-text-black"></i>
                    <span>Bestehenden Account</span>
                </div>
            </button>

            <p class="tw-my-6">mit {{ this.provider }} verknüpfen?</p>

        </div>

        <div v-show="show === 1">
            <slot name="content1"></slot>
        </div>

        <div v-show="show === 2">
            <slot name="content2"></slot>
        </div>


        <div v-show="show === 1 ||show === 2">
            <hr>
            <button class="btn btn-outline-primary" @click="show = 0">Zurück</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['provider'],
        data() {
            return {show: 0};
        },
    };
</script>

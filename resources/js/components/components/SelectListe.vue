<template>
    <div class="tw-max-w-sm tw-mx-auto tw-flex tw-items-center tw-justify-center tw-mb-7">
        <div class="">
            <p class="tw-my-0 tw-mr-4">Liste:</p>
        </div>
        <div class="tw-max-w-2xs">
            <select class="form-control form-control-sm" name="group" id="group"
                    onchange="location = this.value">
                <slot></slot>
            </select>
        </div>
    </div>
</template>

<script>
    export default {
        props: [],

        computed: {},

        data() {
            return {};
        },
    };
</script>

<template>
    <div class="max-w-sm mx-auto flex items-center justify-center mb-7">
        <div class="">
            <p class="my-0 mr-4">Liste:</p>
        </div>
        <div class="max-w-2xs">
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

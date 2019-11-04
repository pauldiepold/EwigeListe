<template>
    <div class="tw-px-2 tw-py-3 tw-flex tw-flex-col">
        <i :class="icon"
           class="fas tw-text-2xl tw-align-middle"></i>
    </div>
</template>

<script>
    export default {
        props: {
            icon: {
                required: true
            },
            text: {
                required: false
            }
        },

        data() {
            return {};
        },
    };
</script>

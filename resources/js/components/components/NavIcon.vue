<template>
    <div class="px-2 py-3 flex flex-col min-w-7xs"
         :title="tooltip">
        <i :class="icon"
           class="fas text-2xl align-middle"></i>
    </div>
</template>

<script>
    export default {
        props: {
            icon: {
                required: true
            },
            tooltip: {
                required: false
            }
        },

        data() {
            return {};
        },
    };
</script>

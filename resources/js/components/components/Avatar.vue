<template>
    <img :src="path"
         :class="['tw-w-' + width, 'tw-h-' + width]"
         class="tw-rounded-full">
</template>

<script>
    export default {
        props: [
            'path',
            'width'
        ],
    };
</script>

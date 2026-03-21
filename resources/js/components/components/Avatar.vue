<template>
    <img :src="path"
         :class="['w-' + width, 'h-' + width]"
         class="rounded-full">
</template>

<script>
    export default {
        props: [
            'path',
            'width'
        ],
    };
</script>

<template>
    <div class="mx-auto" style="max-width: 24rem;">
        <ul>
            <slot/>
        </ul>
    </div>
</template>

<script>
    import {ContainerMixin} from "vue-slicksort";

    export default {
        mixins: [ContainerMixin],
    };
</script>

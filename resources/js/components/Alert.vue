<template>
    <div v-if="show" class="alert alert-success my-2" role="alert">
        <div class="row align-items-center no-gutters">
            <div class="col-11">
                {{ message }}
            </div>
            <div class="col-1">
                <button type="button" class="close" @click="show = false">
                    <i class="fas fa-sm fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['message'],
        data() {
            return {
                show: false
            }
        }
    };
</script>

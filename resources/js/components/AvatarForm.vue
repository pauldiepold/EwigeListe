<template>
    <div class="">
        <img :src="result" class="tw-mx-auto tw-mb-6 tw-h-48 tw-rounded-full">

        <form enctype="multipart/form-data">
            <div class="form-group tw-max-w-sm tw-mx-auto tw-text-center">
                <image-upload name="avatar" @loaded="onFilePicked"></image-upload>
            </div>
        </form>

        <cropper
                v-if="file_picked"
                class="tw-h-64"
                :src="src"
                :stencil-props="{
                    aspectRatio: 1/1
                }"
                @change="change">
        </cropper>

        <button @click="persist"
                v-if="file_picked"
                class="btn btn-primary tw-mt-4 d-flex vertical-align-center mx-auto">
            <span>
                <i v-if="loading"
                   class="fa fa-spinner fa-spin text-lg mr-2"
                   style="font-size:1.2rem; vertical-align: -0.1rem;"></i>
            </span>
            <span>
                Bild speichern
            </span>
        </button>
    </div>
</template>

<script>
    import ImageUpload from './components/ImageUpload.vue'
    import {Cropper} from "vue-advanced-cropper";

    export default {
        components: {
            Cropper,
            ImageUpload
        },

        props: ['user'],

        data() {
            return {
                src: this.user.avatar_path,
                avatar: '',
                result: this.user.avatar_path,
                file_picked: false,
                loading: false
            }
        },

        methods: {
            onFilePicked(avatar) {
                this.src = avatar.src;
                this.avatar = avatar.file;
                this.file_picked = true;
            },

            change({coordinates, canvas}) {
                this.result = canvas.toDataURL();
            },

            persist() {
                if (!this.result) return;

                this.loading = true;

                axios.post(`/api/users/${this.user.id}/avatar`, {avatar: this.result})
                    .then(() => {
                        this.file_picked = false;
                        this.loading = false;
                    });
            }
        }
    };
</script>

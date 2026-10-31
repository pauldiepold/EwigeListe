<script setup lang="ts">
import { onBeforeUnmount, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';
import { updateAvatar } from '@/routes/users';
import type { EditUser } from '@/types/user-edit';

const props = defineProps<{
  user: EditUser;
}>();

/**
 * Der Zuschnitt wird im Browser auf 800 px begrenzt, damit große Handyfotos nicht als riesige
 * Data-URI beim Server ankommen. Transparenz bekommt einen weißen Hintergrund, weil JPEG sie nicht kennt.
 */
const canvasOptions = { maxWidth: 800, maxHeight: 800, fillColor: '#ffffff' };

const file = ref<File | null>(null);
const imageUrl = ref<string | null>(null);

const form = useForm({
  avatar: '',
});

watch(file, (selectedFile) => {
  releaseImageUrl();
  form.avatar = '';
  form.clearErrors('avatar');
  imageUrl.value = selectedFile ? URL.createObjectURL(selectedFile) : null;
});

onBeforeUnmount(releaseImageUrl);

function releaseImageUrl(): void {
  if (imageUrl.value) {
    URL.revokeObjectURL(imageUrl.value);
  }
}

function onCrop({ canvas }: { canvas?: HTMLCanvasElement }): void {
  form.avatar = canvas?.toDataURL('image/jpeg', 0.9) ?? '';
}

function submit(): void {
  form.post(updateAvatar.url(props.user.id), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      file.value = null;
    },
  });
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="submit">
    <div class="flex justify-center">
      <UAvatar
        :src="form.avatar || user.avatar_path || undefined"
        :alt="`${user.surname} ${user.name}`"
        class="size-48 text-5xl"
      />
    </div>

    <UFormField :error="form.errors.avatar">
      <UFileUpload
        v-model="file"
        accept="image/*"
        name="avatar"
        icon="i-lucide-image-up"
        label="Avatar auswählen"
        :preview="false"
        :disabled="form.processing"
        class="w-full"
      />
    </UFormField>

    <template v-if="imageUrl">
      <Cropper
        class="h-64"
        :src="imageUrl"
        :stencil-props="{ aspectRatio: 1 }"
        :canvas="canvasOptions"
        @change="onCrop"
      />

      <UButton
        type="submit"
        color="primary"
        label="Avatar speichern"
        block
        :disabled="!form.avatar"
        :loading="form.processing"
      />
    </template>
  </form>
</template>

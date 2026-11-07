<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { updateMail } from '@/routes/users';
import type { EditUser } from '@/types/user-edit';

const props = defineProps<{
  user: EditUser;
}>();

const form = useForm({
  email: props.user.email,
});

function submit(): void {
  form.patch(updateMail.url(props.user.id), {
    preserveState: true,
    preserveScroll: true,
  });
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="submit">
    <UFormField label="E-Mail" :error="form.errors.email" required>
      <UInput
        v-model="form.email"
        type="email"
        name="email"
        autocomplete="email"
        class="w-full"
        :disabled="form.processing"
      />
    </UFormField>

    <UButton
      type="submit"
      color="primary"
      label="E-Mail speichern"
      block
      :loading="form.processing"
    />
  </form>
</template>

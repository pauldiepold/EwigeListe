<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { updatePassword } from '@/routes/users';
import type { EditUser } from '@/types/user-edit';

const props = defineProps<{
  user: EditUser;
}>();

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

function submit(): void {
  form.patch(updatePassword.url(props.user.id), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="submit">
    <UFormField label="Altes Passwort" :error="form.errors.current_password" required>
      <UInput
        v-model="form.current_password"
        type="password"
        name="current_password"
        autocomplete="current-password"
        class="w-full"
        :disabled="form.processing"
      />
    </UFormField>

    <UFormField label="Neues Passwort" :error="form.errors.password" required>
      <UInput
        v-model="form.password"
        type="password"
        name="password"
        autocomplete="new-password"
        class="w-full"
        :disabled="form.processing"
      />
    </UFormField>

    <UFormField label="Passwort bestätigen" :error="form.errors.password_confirmation" required>
      <UInput
        v-model="form.password_confirmation"
        type="password"
        name="password_confirmation"
        autocomplete="new-password"
        class="w-full"
        :disabled="form.processing"
      />
    </UFormField>

    <UButton
      type="submit"
      color="primary"
      label="Passwort speichern"
      block
      :loading="form.processing"
    />
  </form>
</template>

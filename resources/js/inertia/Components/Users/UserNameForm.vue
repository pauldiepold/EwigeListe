<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { updateName } from '@/routes/users';
import type { EditUser } from '@/types/user-edit';

const props = defineProps<{
  user: EditUser;
}>();

const form = useForm({
  first_name: props.user.firstName,
  last_name: props.user.lastName,
});

function submit(): void {
  form.patch(updateName.url(props.user.id), {
    preserveState: true,
    preserveScroll: true,
  });
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="submit">
    <UFormField label="Vorname" :error="form.errors.first_name" required>
      <UInput
        v-model="form.first_name"
        name="first_name"
        autocomplete="given-name"
        class="w-full"
        :disabled="form.processing"
      />
    </UFormField>

    <UFormField label="Nachname" :error="form.errors.last_name" required>
      <UInput
        v-model="form.last_name"
        name="last_name"
        autocomplete="family-name"
        class="w-full"
        :disabled="form.processing"
      />
    </UFormField>

    <UButton
      type="submit"
      color="primary"
      label="Namen speichern"
      block
      :loading="form.processing"
    />
  </form>
</template>

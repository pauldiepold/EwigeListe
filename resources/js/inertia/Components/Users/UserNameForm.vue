<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { updateName } from '@/routes/users';
import type { EditUser } from '@/types/user-edit';

const props = defineProps<{
  user: EditUser;
}>();

const form = useForm({
  vorname: props.user.surname,
  nachname: props.user.name,
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
    <UFormField label="Vorname" :error="form.errors.vorname" required>
      <UInput
        v-model="form.vorname"
        name="vorname"
        autocomplete="given-name"
        class="w-full"
        :disabled="form.processing"
      />
    </UFormField>

    <UFormField label="Nachname" :error="form.errors.nachname" required>
      <UInput
        v-model="form.nachname"
        name="nachname"
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

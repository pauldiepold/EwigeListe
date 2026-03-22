<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAppRoute } from '@/composables/useAppRoute';

const { route } = useAppRoute();

const form = useForm({
  surname: '',
  name: '',
  email: '',
});

function submit(): void {
  form.post(route('register.quick.store'));
}
</script>

<template>
  <Head title="Neue Person registrieren" />

  <AppLayout title="Neue Person registrieren">
    <div class="mx-auto w-full max-w-md space-y-6">
      <UAlert
        color="info"
        variant="soft"
        icon="i-lucide-info"
        title="Hier kannst du einen neuen User registrieren."
        description="Wenn eine korrekte E-Mail-Adresse angegeben wird, kann das Passwort später zurückgesetzt werden."
      />

      <UCard>
        <form class="space-y-4" @submit.prevent="submit">
          <UFormField label="Vorname" :error="form.errors.surname" required>
            <UInput
              v-model="form.surname"
              name="surname"
              autocomplete="given-name"
              autofocus
              :disabled="form.processing"
            />
          </UFormField>

          <UFormField label="Nachname" :error="form.errors.name" required>
            <UInput
              v-model="form.name"
              name="name"
              autocomplete="family-name"
              :disabled="form.processing"
            />
          </UFormField>

          <UFormField label="E-Mail" :error="form.errors.email" required>
            <UInput
              v-model="form.email"
              type="email"
              name="email"
              autocomplete="email"
              :disabled="form.processing"
            />
          </UFormField>

          <div class="flex gap-3">
            <UButton
              type="submit"
              color="primary"
              icon="i-lucide-user-plus"
              label="User registrieren"
              :loading="form.processing"
            />
            <UButton
              :to="route('rounds.create')"
              color="neutral"
              variant="outline"
              icon="i-lucide-arrow-left"
              label="Zurück"
            />
          </div>
        </form>
      </UCard>
    </div>
  </AppLayout>
</template>

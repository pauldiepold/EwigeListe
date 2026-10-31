<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import type { FormSubmitEvent } from '@nuxt/ui';
import AppLayout from '@/Layouts/AppLayout.vue';
import { login } from '@/routes';
import { email as passwordEmail } from '@/routes/password';

type ForgotData = { email: string };

const form = useForm<ForgotData>({ email: '' });

const fields = computed(() => [
  {
    name: 'email',
    type: 'email',
    label: 'E-Mail Adresse',
    placeholder: 'deine@mail.de',
    autocomplete: 'email',
    required: true,
    error: form.errors.email,
  },
]);

function onSubmit(event: FormSubmitEvent<ForgotData>): void {
  form.email = event.data.email;
  form.post(passwordEmail.url());
}
</script>

<template>
  <Head title="Passwort vergessen" />

  <AppLayout>
    <div class="flex items-center justify-center py-12">
      <UPageCard class="w-full max-w-md">
        <UAuthForm
          title="Passwort vergessen?"
          description="Gib deine E-Mail ein und wir senden dir einen Link zum Zurücksetzen."
          icon="i-lucide-mail"
          :fields="fields"
          :submit="{ label: 'Link senden', loading: form.processing }"
          @submit="onSubmit"
        >
          <template #footer>
            <ULink :to="login.url()" class="text-primary font-medium">Zurück zum Login</ULink>
          </template>
        </UAuthForm>
      </UPageCard>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import type { FormSubmitEvent } from '@nuxt/ui';
import AppLayout from '@/Layouts/AppLayout.vue';
import { login, register } from '@/routes';
import { socialite } from '@/routes/auth';
import { request as passwordRequest } from '@/routes/password';

type LoginData = {
  email: string;
  password: string;
  remember: boolean;
};

const form = useForm<LoginData>({
  email: '',
  password: '',
  remember: true,
});

const fields = computed(() => [
  {
    name: 'email',
    type: 'email',
    label: 'E-Mail',
    placeholder: 'deine@mail.de',
    autocomplete: 'email',
    required: true,
    error: form.errors.email,
  },
  {
    name: 'password',
    type: 'password',
    label: 'Passwort',
    autocomplete: 'current-password',
    required: true,
    error: form.errors.password,
  },
  {
    name: 'remember',
    type: 'checkbox',
    label: 'Angemeldet bleiben',
  },
]);

const providers = computed(() => [
  {
    label: 'Mit Google anmelden',
    icon: 'i-lucide-globe',
    color: 'neutral' as const,
    variant: 'outline' as const,
    onClick: () => {
      window.location.href = socialite.url('google');
    },
  },
  {
    label: 'Mit Facebook anmelden',
    icon: 'i-lucide-users',
    color: 'neutral' as const,
    variant: 'outline' as const,
    onClick: () => {
      window.location.href = socialite.url('facebook');
    },
  },
]);

function onSubmit(event: FormSubmitEvent<LoginData>): void {
  form.email = event.data.email;
  form.password = event.data.password;
  form.remember = event.data.remember;
  form.post(login.url(), {
    onFinish: () => form.reset('password'),
  });
}
</script>

<template>
  <Head title="Anmelden" />

  <AppLayout>
    <div class="flex items-center justify-center py-12">
      <UPageCard class="w-full max-w-md">
        <UAuthForm
          title="Willkommen zurück"
          icon="i-lucide-lock"
          :fields="fields"
          :providers="providers"
          :submit="{ label: 'Anmelden', loading: form.processing }"
          @submit="onSubmit"
        >
          <template #password-hint>
            <ULink :to="passwordRequest.url()" class="text-primary font-medium" tabindex="-1">
              Passwort vergessen?
            </ULink>
          </template>

          <template #footer>
            Noch kein Konto?
            <ULink :to="register.url()" class="text-primary font-medium">Registrieren</ULink>
          </template>
        </UAuthForm>
      </UPageCard>
    </div>
  </AppLayout>
</template>

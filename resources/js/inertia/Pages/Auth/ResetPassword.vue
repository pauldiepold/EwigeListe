<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import type { FormSubmitEvent } from '@nuxt/ui';
import AppLayout from '@/Layouts/AppLayout.vue';
import { update as passwordUpdate } from '@/routes/password';
import type { ResetPasswordProps } from '@/types/auth';

const props = defineProps<ResetPasswordProps>();

type ResetData = {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
};

const form = useForm<ResetData>({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const fields = computed(() => [
  {
    name: 'email',
    type: 'email',
    label: 'E-Mail Adresse',
    autocomplete: 'email',
    required: true,
    error: form.errors.email,
  },
  {
    name: 'password',
    type: 'password',
    label: 'Neues Passwort',
    autocomplete: 'new-password',
    required: true,
    error: form.errors.password,
  },
  {
    name: 'password_confirmation',
    type: 'password',
    label: 'Passwort bestätigen',
    autocomplete: 'new-password',
    required: true,
    error: form.errors.password_confirmation,
  },
]);

function onSubmit(event: FormSubmitEvent<ResetData>): void {
  form.email = event.data.email;
  form.password = event.data.password;
  form.password_confirmation = event.data.password_confirmation;
  form.post(passwordUpdate.url(), {
    onFinish: () => {
      form.reset('password', 'password_confirmation');
    },
  });
}
</script>

<template>
  <Head title="Passwort zurücksetzen" />

  <AppLayout>
    <div class="flex items-center justify-center py-12">
      <UPageCard class="w-full max-w-md">
        <UAuthForm
          title="Neues Passwort"
          description="Gib dein neues Passwort ein."
          icon="i-lucide-key-round"
          :fields="fields"
          :submit="{ label: 'Passwort zurücksetzen', loading: form.processing }"
          @submit="onSubmit"
        />
      </UPageCard>
    </div>
  </AppLayout>
</template>

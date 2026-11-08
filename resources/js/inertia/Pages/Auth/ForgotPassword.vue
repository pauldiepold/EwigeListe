<script setup lang="ts">
import { computed, ref } from 'vue';
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

/** Adresse, an die der Link ging; solange gesetzt, ersetzt die Bestätigung das Formular. */
const sentTo = ref<string | null>(null);

function onSubmit(event: FormSubmitEvent<ForgotData>): void {
  form.email = event.data.email;
  form.post(passwordEmail.url(), {
    preserveState: true,
    onSuccess: () => {
      sentTo.value = form.email;
    },
  });
}
</script>

<template>
  <Head title="Passwort vergessen" />

  <AppLayout>
    <div class="flex items-center justify-center py-12">
      <UPageCard
        v-if="sentTo"
        class="w-full max-w-md"
        icon="i-lucide-mail-check"
        title="Schau in dein Postfach"
      >
        <p class="text-muted">
          Wir haben einen Link zum Zurücksetzen an
          <strong class="text-highlighted">{{ sentTo }}</strong> geschickt. Klicke ihn an, um ein
          neues Passwort festzulegen.
        </p>
        <p class="text-sm text-muted">
          Keine Mail bekommen? Schau im Spam-Ordner nach oder fordere den Link erneut an.
        </p>
        <div class="flex flex-wrap items-center justify-between gap-3">
          <UButton
            color="neutral"
            variant="outline"
            icon="i-lucide-rotate-ccw"
            label="Erneut senden"
            @click="sentTo = null"
          />
          <ULink :to="login.url()" class="text-primary font-medium">Zurück zum Login</ULink>
        </div>
      </UPageCard>

      <UPageCard v-else class="w-full max-w-md">
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

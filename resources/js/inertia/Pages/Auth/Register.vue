<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { login, register } from '@/routes';
import { socialite } from '@/routes/auth';
import type { RegisterProps } from '@/types/auth';

const props = defineProps<RegisterProps>();

const captchaWidgetId = ref<number | null>(null);
const captchaContainer = ref<HTMLDivElement | null>(null);

const form = useForm({
  surname: '',
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  'g-recaptcha-response': '',
});

function resetCaptcha(): void {
  if (captchaWidgetId.value !== null && window.grecaptcha) {
    window.grecaptcha.reset(captchaWidgetId.value);
    form['g-recaptcha-response'] = '';
  }
}

function navigateToProvider(provider: string): void {
  window.location.href = socialite.url(provider);
}

function submit(): void {
  form.post(register.url(), {
    onError: resetCaptcha,
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
}

onMounted(() => {
  const scriptId = 'recaptcha-script';
  if (!document.getElementById(scriptId)) {
    const script = document.createElement('script');
    script.id = scriptId;
    script.src = 'https://www.google.com/recaptcha/api.js?render=explicit&hl=de';
    script.async = true;
    script.defer = true;
    script.onload = () => renderCaptcha();
    document.head.appendChild(script);
  } else if (window.grecaptcha) {
    renderCaptcha();
  }
});

function renderCaptcha(): void {
  if (!captchaContainer.value || captchaWidgetId.value !== null) {
    return;
  }
  captchaWidgetId.value = window.grecaptcha.render(captchaContainer.value, {
    sitekey: props.captchaSiteKey,
    callback: (token: string) => {
      form['g-recaptcha-response'] = token;
    },
    'expired-callback': () => {
      form['g-recaptcha-response'] = '';
    },
  });
}
</script>

<template>
  <Head title="Registrieren" />

  <AppLayout>
    <div class="mx-auto w-full max-w-md space-y-6 py-8">
      <UCard>
        <template #header>
          <h2 class="text-lg font-semibold">Registrieren</h2>
        </template>

        <div class="space-y-6">
          <div class="space-y-3">
            <UButton
              block
              color="neutral"
              variant="outline"
              icon="i-lucide-globe"
              label="Mit Google registrieren"
              @click="navigateToProvider('google')"
            />
            <UButton
              block
              color="neutral"
              variant="outline"
              icon="i-lucide-users"
              label="Mit Facebook registrieren"
              @click="navigateToProvider('facebook')"
            />
          </div>

          <USeparator label="Oder mit E-Mail registrieren" />

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

            <UFormField label="Passwort" :error="form.errors.password" required>
              <UInput
                v-model="form.password"
                type="password"
                name="password"
                autocomplete="new-password"
                :disabled="form.processing"
              />
            </UFormField>

            <UFormField label="Passwort bestätigen" required>
              <UInput
                v-model="form.password_confirmation"
                type="password"
                name="password_confirmation"
                autocomplete="new-password"
                :disabled="form.processing"
              />
            </UFormField>

            <UFormField :error="form.errors['g-recaptcha-response']">
              <div ref="captchaContainer" />
            </UFormField>

            <UButton
              type="submit"
              color="primary"
              block
              icon="i-lucide-user-plus"
              label="Registrieren"
              :loading="form.processing"
            />
          </form>

          <p class="text-center text-sm text-muted">
            Bereits registriert?
            <ULink :to="login.url()" class="text-primary font-medium">Anmelden</ULink>
          </p>
        </div>
      </UCard>
    </div>
  </AppLayout>
</template>

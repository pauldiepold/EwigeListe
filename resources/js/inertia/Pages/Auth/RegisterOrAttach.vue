<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAppRoute } from '@/composables/useAppRoute';
import type { RegisterOrAttachProps } from '@/types/auth';

const props = defineProps<RegisterOrAttachProps>();

const { route } = useAppRoute();

type Step = 'choose' | 'register' | 'login';
const step = ref<Step>(props.existingUserEmail ? 'login' : 'choose');

const providerLabel =
  props.socialiteUser.provider.charAt(0).toUpperCase() + props.socialiteUser.provider.slice(1);

const registerForm = useForm({
  email: props.socialiteUser.email,
  surname: props.socialiteUser.surname,
  name: props.socialiteUser.name,
  socialiteUserId: props.socialiteUser.id,
});

const loginForm = useForm({
  email: '',
  password: '',
  remember: true,
  socialiteUserId: props.socialiteUser.id,
});

function submitRegister(): void {
  registerForm.post(route('register'));
}

function submitLogin(): void {
  loginForm.post(route('login'), {
    onFinish: () => loginForm.reset('password'),
  });
}
</script>

<template>
  <Head :title="`${providerLabel} Login`" />

  <AppLayout :title="`${providerLabel} Login`">
    <div class="mx-auto w-full max-w-md space-y-6">
      <div v-if="step === 'choose'">
        <UCard>
          <template #header>
            <h2 class="text-lg font-semibold">Mit {{ providerLabel }} verknüpfen</h2>
          </template>

          <p class="mb-6 text-muted">
            Möchtest du einen neuen Account erstellen oder deinen bestehenden Account mit
            {{ providerLabel }} verknüpfen?
          </p>

          <div class="flex flex-col gap-3">
            <UButton
              block
              color="primary"
              icon="i-lucide-user-plus"
              label="Neuen Account erstellen"
              @click="step = 'register'"
            />
            <UButton
              block
              color="neutral"
              variant="outline"
              icon="i-lucide-log-in"
              label="Bestehenden Account verknüpfen"
              @click="step = 'login'"
            />
          </div>
        </UCard>
      </div>

      <div v-else-if="step === 'register'">
        <UCard>
          <template #header>
            <div class="flex items-center gap-3">
              <UButton
                icon="i-lucide-arrow-left"
                color="neutral"
                variant="ghost"
                size="xs"
                @click="step = 'choose'"
              />
              <h2 class="text-lg font-semibold">Neuen Account mit {{ providerLabel }} erstellen</h2>
            </div>
          </template>

          <form class="space-y-4" @submit.prevent="submitRegister">
            <UFormField label="Vorname" :error="registerForm.errors.surname" required>
              <UInput
                v-model="registerForm.surname"
                name="surname"
                autocomplete="given-name"
                autofocus
                :disabled="registerForm.processing"
              />
            </UFormField>

            <UFormField label="Nachname" :error="registerForm.errors.name" required>
              <UInput
                v-model="registerForm.name"
                name="name"
                autocomplete="family-name"
                :disabled="registerForm.processing"
              />
            </UFormField>

            <UFormField label="E-Mail" :error="registerForm.errors.email">
              <UInput v-model="registerForm.email" type="email" name="email" disabled />
            </UFormField>

            <UButton
              type="submit"
              color="primary"
              block
              icon="i-lucide-user-plus"
              :label="`Mit ${providerLabel} registrieren`"
              :loading="registerForm.processing"
            />
          </form>
        </UCard>
      </div>

      <div v-else>
        <UCard>
          <template #header>
            <div class="flex items-center gap-3">
              <UButton
                v-if="!existingUserEmail"
                icon="i-lucide-arrow-left"
                color="neutral"
                variant="ghost"
                size="xs"
                @click="step = 'choose'"
              />
              <h2 class="text-lg font-semibold">Account mit {{ providerLabel }} verknüpfen</h2>
            </div>
          </template>

          <div class="space-y-4">
            <UAlert
              v-if="existingUserEmail"
              color="info"
              variant="soft"
              icon="i-lucide-info"
              :description="`Es existiert bereits ein Account mit der E-Mail ${existingUserEmail}. Melde dich an, um ihn mit ${providerLabel} zu verknüpfen.`"
            />

            <p class="text-sm text-muted">
              Gib deine Anmeldedaten ein, um deinen Account mit {{ providerLabel }} zu verknüpfen.
              In Zukunft kannst du dich mit einem Klick einloggen!
            </p>

            <form class="space-y-4" @submit.prevent="submitLogin">
              <UFormField label="E-Mail" :error="loginForm.errors.email" required>
                <UInput
                  v-model="loginForm.email"
                  type="email"
                  name="email"
                  autocomplete="email"
                  autofocus
                  :disabled="loginForm.processing"
                />
              </UFormField>

              <UFormField label="Passwort" :error="loginForm.errors.password" required>
                <UInput
                  v-model="loginForm.password"
                  type="password"
                  name="password"
                  autocomplete="current-password"
                  :disabled="loginForm.processing"
                />
              </UFormField>

              <UCheckbox v-model="loginForm.remember" name="remember" label="Angemeldet bleiben" />

              <div class="flex items-center justify-between">
                <UButton
                  type="submit"
                  color="primary"
                  icon="i-lucide-link"
                  :label="`Mit ${providerLabel} verknüpfen`"
                  :loading="loginForm.processing"
                />
                <ULink :to="route('password.request')" class="text-sm text-primary"
                  >Passwort vergessen?</ULink
                >
              </div>
            </form>
          </div>
        </UCard>
      </div>
    </div>
  </AppLayout>
</template>

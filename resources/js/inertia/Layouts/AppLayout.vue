<script setup lang="ts">
import { computed, onMounted, provide, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
  datenschutz,
  ewigeListe,
  impressum,
  index as home,
  login,
  logout as logoutRoute,
  regeln,
  register,
} from '@/routes';
import { show as showPlayer } from '@/actions/App/Http/Controllers/PlayerController';
import {
  create as createRound,
  current as currentRound,
  index as roundsIndex,
} from '@/routes/rounds';
import { inertiaColorModeKey, type InertiaColorMode } from '@/composables/inertiaColorMode';

defineProps<{
  title?: string;
}>();

const colorMode = ref<InertiaColorMode>('light');

provide(inertiaColorModeKey, colorMode);
const page = usePage();

const isDark = computed(() => colorMode.value === 'dark');
const isAuthenticated = computed(() => page.props.auth.user !== null);
const authPlayer = computed(() => page.props.auth.player);
const canOpenCurrentRound = computed(() => page.props.navigation.showCurrentRound);

function applyColorMode(mode: InertiaColorMode): void {
  colorMode.value = mode;
  const root = document.documentElement;
  root.classList.toggle('dark', mode === 'dark');
  localStorage.setItem('ui-color-mode', mode);
}

function toggleColorMode(): void {
  applyColorMode(colorMode.value === 'dark' ? 'light' : 'dark');
}

function logout(): void {
  router.post(logoutRoute.url());
}

onMounted(() => {
  const stored = localStorage.getItem('ui-color-mode');
  if (stored === 'dark' || stored === 'light') {
    applyColorMode(stored);
    return;
  }

  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  applyColorMode(prefersDark ? 'dark' : 'light');
});
</script>

<template>
  <div class="flex min-h-screen flex-col bg-default text-highlighted">
    <header class="shrink-0">
      <div class="bg-blue-dark py-2 text-center">
        <Link :href="home.url()" class="text-lg font-semibold text-white no-underline"
          >Ewige Liste</Link
        >
      </div>

      <nav class="bg-blue">
        <UContainer class="max-w-md px-1 py-2">
          <div class="flex items-center justify-around">
            <UButton
              :to="home.url()"
              color="neutral"
              variant="ghost"
              class="text-white hover:bg-white/15"
              icon="i-lucide-house"
              aria-label="Startseite"
            />

            <template v-if="isAuthenticated">
              <UButton
                v-if="canOpenCurrentRound"
                :to="currentRound.url()"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-circle-play"
                aria-label="Aktuelle Runde"
              />
              <UButton
                :to="createRound.url()"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-circle-plus"
                aria-label="Neue Runde"
              />
              <UButton
                :to="ewigeListe.url()"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-list"
                aria-label="Ewige Liste"
              />
              <UButton
                :to="roundsIndex.url()"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-history"
                aria-label="Rundenarchiv"
              />
            </template>

            <template v-else>
              <UButton
                :to="login.url()"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-log-in"
                aria-label="Login"
              />
              <UButton
                :to="register.url()"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-user-plus"
                aria-label="Registrieren"
              />
            </template>
          </div>
        </UContainer>
      </nav>
    </header>

    <UPage as="main" class="flex-1">
      <UContainer class="w-full max-w-4xl py-8">
        <UPageHeader
          v-if="title"
          :title="title"
          :ui="{
            root: 'relative border-b border-default py-4 sm:py-5',
            title: 'text-xl sm:text-2xl text-pretty font-semibold text-highlighted',
          }"
        />
        <UPageBody>
          <slot />
        </UPageBody>
      </UContainer>
    </UPage>

    <footer class="shrink-0 border-t border-default bg-elevated">
      <UContainer
        class="flex w-full max-w-4xl flex-col gap-3 py-4 text-sm sm:flex-row sm:items-center sm:justify-between"
      >
        <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 sm:justify-start">
          <template v-if="authPlayer">
            <span class="text-muted">
              Angemeldet:
              <Link
                :href="showPlayer.url({ player: authPlayer.id })"
                class="text-muted no-underline hover:text-highlighted hover:underline"
                >{{ authPlayer.surname }} {{ authPlayer.name }}</Link
              >
            </span>
            <span class="text-muted" aria-hidden="true">&bull;</span>
          </template>
          <Link
            :href="regeln.url()"
            class="text-muted no-underline hover:text-highlighted hover:underline"
          >
            Regeln
          </Link>
          <span class="text-muted" aria-hidden="true">&bull;</span>
          <Link
            :href="impressum.url()"
            class="text-muted no-underline hover:text-highlighted hover:underline"
          >
            Impressum
          </Link>
          <span class="text-muted" aria-hidden="true">&bull;</span>
          <Link
            :href="datenschutz.url()"
            class="text-muted no-underline hover:text-highlighted hover:underline"
          >
            Datenschutz
          </Link>
          <template v-if="isAuthenticated">
            <span class="text-muted" aria-hidden="true">&bull;</span>
            <button
              type="button"
              class="text-muted hover:text-highlighted hover:underline"
              @click="logout"
            >
              Logout
            </button>
          </template>
          <template v-else>
            <span class="text-muted" aria-hidden="true">&bull;</span>
            <Link
              :href="login.url()"
              class="text-muted no-underline hover:text-highlighted hover:underline"
            >
              Login
            </Link>
          </template>
        </div>
        <div class="flex items-center justify-center gap-4 sm:justify-end">
          <span class="text-muted"
            >&copy; Paul Diepold <span class="text-muted" aria-hidden="true">&bull;</span>
            {{ new Date().getFullYear() }}</span
          >
          <UButton
            color="neutral"
            variant="ghost"
            :icon="isDark ? 'i-lucide-sun' : 'i-lucide-moon'"
            :label="isDark ? 'Hell' : 'Dunkel'"
            @click="toggleColorMode"
          />
        </div>
      </UContainer>
    </footer>
  </div>
</template>

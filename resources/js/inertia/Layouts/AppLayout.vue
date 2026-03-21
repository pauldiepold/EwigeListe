<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useAppRoute } from '@/composables/useAppRoute';

defineProps<{
  title?: string;
}>();

type ColorMode = 'light' | 'dark';

const colorMode = ref<ColorMode>('light');
const page = usePage<{
  auth?: { user?: { id: number; player_id: number | null } | null };
  navigation?: { showCurrentRound?: boolean };
}>();

const { route } = useAppRoute();

const isDark = computed(() => colorMode.value === 'dark');
const isAuthenticated = computed(() => Boolean(page.props.auth?.user));
const canOpenCurrentRound = computed(() => Boolean(page.props.navigation?.showCurrentRound));

function applyColorMode(mode: ColorMode): void {
  colorMode.value = mode;
  const root = document.documentElement;
  root.classList.toggle('dark', mode === 'dark');
  localStorage.setItem('ui-color-mode', mode);
}

function toggleColorMode(): void {
  applyColorMode(colorMode.value === 'dark' ? 'light' : 'dark');
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
    <header class="z-50 shrink-0">
      <div class="bg-blue-dark py-2 text-center">
        <Link :href="route('index')" class="text-lg font-semibold text-white no-underline">Ewige Liste</Link>
      </div>

      <nav class="bg-blue">
        <UContainer class="max-w-md px-1 py-2">
          <div class="flex items-center justify-around">
            <UButton
              :to="route('index')"
              color="neutral"
              variant="ghost"
              class="text-white hover:bg-white/15"
              icon="i-lucide-house"
              aria-label="Startseite"
            />

            <template v-if="isAuthenticated">
              <UButton
                v-if="canOpenCurrentRound"
                :to="route('rounds.current')"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-circle-play"
                aria-label="Aktuelle Runde"
              />
              <UButton
                :to="route('rounds.create')"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-circle-plus"
                aria-label="Neue Runde"
              />
              <UButton
                :to="route('groups.index')"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-list"
                aria-label="Listen"
              />
              <UButton
                :to="route('rounds.index')"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-history"
                aria-label="Rundenarchiv"
              />
            </template>

            <template v-else>
              <UButton
                :to="route('login')"
                color="neutral"
                variant="ghost"
                class="text-white hover:bg-white/15"
                icon="i-lucide-log-in"
                aria-label="Login"
              />
              <UButton
                :to="route('register')"
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
          <Link :href="route('regeln')" class="text-muted no-underline hover:text-highlighted hover:underline">
            Regeln
          </Link>
          <span class="text-muted" aria-hidden="true">&bull;</span>
          <Link :href="route('impressum')" class="text-muted no-underline hover:text-highlighted hover:underline">
            Impressum
          </Link>
          <span class="text-muted" aria-hidden="true">&bull;</span>
          <Link :href="route('datenschutz')" class="text-muted no-underline hover:text-highlighted hover:underline">
            Datenschutz
          </Link>
        </div>
        <div class="flex items-center justify-center gap-4 sm:justify-end">
          <span class="text-muted">&copy; {{ new Date().getFullYear() }} Ewige Liste</span>
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

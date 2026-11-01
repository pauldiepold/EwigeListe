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

const currentPath = computed(() => page.url.split(/[?#]/)[0]);

const navItems = computed(() =>
  isAuthenticated.value
    ? [
        {
          to: home.url(),
          icon: 'i-lucide-house',
          label: 'Startseite',
          active: ['/', '/home'].includes(currentPath.value),
        },
        ...(canOpenCurrentRound.value
          ? [
              {
                to: currentRound.url(),
                icon: 'i-lucide-circle-play',
                label: 'Aktuelle Runde',
                active: /^\/runde\/\d/.test(currentPath.value),
              },
            ]
          : []),
        {
          to: createRound.url(),
          icon: 'i-lucide-circle-plus',
          label: 'Neue Runde',
          active: currentPath.value === createRound.url(),
        },
        {
          to: ewigeListe.url(),
          icon: 'i-lucide-list',
          label: 'Ewige Liste',
          active: currentPath.value.startsWith('/liste'),
        },
        {
          to: roundsIndex.url(),
          icon: 'i-lucide-history',
          label: 'Rundenarchiv',
          active: currentPath.value.startsWith('/rundenarchiv'),
        },
      ]
    : [
        {
          to: home.url(),
          icon: 'i-lucide-house',
          label: 'Startseite',
          active: ['/', '/home'].includes(currentPath.value),
        },
        {
          to: login.url(),
          icon: 'i-lucide-log-in',
          label: 'Login',
          active: currentPath.value === login.url(),
        },
        {
          to: register.url(),
          icon: 'i-lucide-user-plus',
          label: 'Registrieren',
          active: currentPath.value === register.url(),
        },
      ],
);

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
  <!-- `isolate`: Stehende Tabellenköpfe und die Aktionsleiste bleiben so unter Dialogen. -->
  <div class="isolate flex min-h-screen flex-col bg-default text-highlighted">
    <header class="shrink-0">
      <div class="bg-blue-dark py-2 text-center">
        <Link :href="home.url()" class="text-lg font-semibold text-white no-underline"
          >Ewige Liste</Link
        >
      </div>

      <nav class="bg-blue">
        <UContainer class="max-w-md px-1 py-1 sm:max-w-3xl">
          <div class="flex items-center justify-around">
            <UButton
              v-for="item in navItems"
              :key="item.to"
              :to="item.to"
              color="neutral"
              variant="ghost"
              size="xl"
              class="text-white hover:bg-white/15"
              :class="{ 'spike-nav-active': item.active }"
              :icon="item.icon"
              :aria-label="item.label"
              :aria-current="item.active ? 'page' : undefined"
            >
              <span class="hidden text-sm whitespace-nowrap sm:inline">{{ item.label }}</span>
            </UButton>
          </div>
        </UContainer>
      </nav>
    </header>

    <UPage as="main" class="flex-1">
      <UContainer class="w-full max-w-4xl py-4 sm:py-8">
        <UPageHeader
          v-if="title"
          :title="title"
          :ui="{
            root: 'relative border-b border-default py-4 sm:py-5 design-b:border-b-0 design-b:text-center design-c:border-b-0 design-c:pt-2 design-d:border-b-0 design-d:py-2',
            wrapper: 'design-b:lg:justify-center',
            title:
              'text-xl sm:text-2xl text-pretty font-semibold text-highlighted design-b:text-sm design-b:sm:text-sm design-b:font-bold design-b:uppercase design-b:tracking-[5px] design-c:text-3xl design-c:sm:text-3xl design-c:font-bold design-d:font-extrabold',
          }"
        />
        <UPageBody class="mt-2 pb-8 sm:mt-4">
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

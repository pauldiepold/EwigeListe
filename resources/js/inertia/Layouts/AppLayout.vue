<script setup lang="ts">
import { computed, onMounted, provide, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import {
  datenschutz,
  ewigeListe,
  impressum,
  home as homeAlias,
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
  /** Inhalt als Flex-Spalte über die volle Höhe, z. B. um etwas ans untere Ende zu schieben. */
  fillHeight?: boolean;
}>();

const colorMode = ref<InertiaColorMode>('light');

provide(inertiaColorModeKey, colorMode);
const page = usePage();

const isDark = computed(() => colorMode.value === 'dark');
const isAuthenticated = computed(() => page.props.auth.user !== null);
const authPlayer = computed(() => page.props.auth.player);
const canOpenCurrentRound = computed(() => page.props.navigation.showCurrentRound);

const currentPath = computed(() => page.url.split(/[?#]/)[0]);

interface NavItem {
  to: string;
  icon: string;
  label: string;
  active: boolean;
}

const navItems = computed<NavItem[]>(() => {
  const homeItem: NavItem = {
    to: home.url(),
    icon: 'i-lucide-house',
    label: 'Startseite',
    active: [home.url(), homeAlias.url()].includes(currentPath.value),
  };

  if (!isAuthenticated.value) {
    return [
      homeItem,
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
    ];
  }

  return [
    homeItem,
    ...(canOpenCurrentRound.value
      ? [
          {
            to: currentRound.url(),
            icon: 'i-lucide-circle-play',
            label: 'Aktuelle Runde',
            // Jede geöffnete Runde zählt als Runden-Ansicht, auch eine aus dem Archiv.
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
      // Gewollt per Präfix: auch `/listen`, `/liste/erstellen` und jede andere Liste.
      active: currentPath.value.startsWith(ewigeListe.url()),
    },
    {
      to: roundsIndex.url(),
      icon: 'i-lucide-history',
      label: 'Rundenarchiv',
      active: currentPath.value.startsWith(roundsIndex.url()),
    },
  ];
});

const footerLinkClass = 'text-muted no-underline hover:text-highlighted hover:underline';

const legalLinks = [
  { href: regeln.url(), label: 'Regeln' },
  { href: impressum.url(), label: 'Impressum' },
  { href: datenschutz.url(), label: 'Datenschutz' },
];

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
  <!-- `isolate`: Stehende Tabellenköpfe und der schwebende Button bleiben so unter Dialogen. -->
  <div class="isolate bg-default text-highlighted">
    <!-- Kopf und Inhalt füllen den Bildschirm, der Footer folgt erst nach dem Scrollen. -->
    <div class="flex min-h-dvh flex-col">
      <header class="shrink-0">
        <div class="bg-blue-dark py-2 text-center">
          <Link :href="home.url()" class="text-lg font-semibold text-white no-underline"
            >Ewige Liste</Link
          >
        </div>

        <nav class="bg-blue">
          <UContainer class="max-w-md px-1 py-1 sm:max-w-4xl">
            <div class="flex items-center justify-around sm:gap-3">
              <UButton
                v-for="item in navItems"
                :key="item.to"
                :to="item.to"
                color="neutral"
                variant="ghost"
                size="xl"
                class="text-white hover:bg-white/15"
                :class="{
                  'rounded-none shadow-[inset_0_-3px_0_var(--ui-color-primary-400)]': item.active,
                }"
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

      <main class="flex flex-1 flex-col">
        <UContainer class="flex w-full max-w-4xl flex-1 flex-col py-4 sm:py-8">
          <UPageHeader
            v-if="title"
            :title="title"
            :ui="{
              root: 'relative border-b border-default py-4 sm:py-5',
              title: 'text-xl sm:text-2xl text-pretty font-semibold text-highlighted',
            }"
          />
          <UPageBody class="mt-2 flex-1 pb-8 sm:mt-4" :class="{ 'flex flex-col': fillHeight }">
            <slot />
          </UPageBody>
        </UContainer>
      </main>
    </div>

    <!-- Außerhalb von `UPageBody`: Dessen `space-y-*` gäbe dem Button sonst einen Außenabstand. -->
    <div
      v-if="$slots.floatingAction"
      class="fixed right-4 bottom-[calc(1rem+env(safe-area-inset-bottom))] z-20"
    >
      <slot name="floatingAction" />
    </div>

    <!-- Mit schwebendem Button bleibt unter dem Seitenende Platz, damit er nichts verdeckt. -->
    <footer
      class="shrink-0 border-t border-default bg-elevated"
      :class="{ 'pb-22': $slots.floatingAction }"
    >
      <!-- Handy: Konto, Rechtliches, Copyright untereinander. Ab `sm`: Rechtliches links, Konto rechts. -->
      <UContainer class="grid w-full max-w-4xl gap-3 py-4 text-sm sm:grid-cols-[1fr_auto]">
        <div class="flex items-center justify-between gap-4 sm:order-2 sm:justify-end">
          <template v-if="isAuthenticated">
            <Link
              v-if="authPlayer"
              :href="showPlayer.url({ player: authPlayer.id })"
              class="flex min-w-0 items-center gap-1.5"
              :class="footerLinkClass"
            >
              <UIcon name="i-lucide-user-round" class="size-4 shrink-0" />
              <span class="truncate">{{ authPlayer.surname }} {{ authPlayer.name }}</span>
            </Link>
            <button type="button" :class="footerLinkClass" @click="logout">Logout</button>
          </template>
          <template v-else>
            <Link :href="login.url()" :class="footerLinkClass">Login</Link>
            <Link :href="register.url()" :class="footerLinkClass">Registrieren</Link>
          </template>
        </div>

        <!-- Kein `nav`: Die Legacy-Regel `nav a` in `app.css` färbt Links sonst hellblau (bis #37). -->
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
          <template v-for="(item, index) in legalLinks" :key="item.href">
            <span v-if="index > 0" class="text-muted" aria-hidden="true">&middot;</span>
            <Link :href="item.href" :class="footerLinkClass">{{ item.label }}</Link>
          </template>
        </div>

        <div class="flex items-center justify-between gap-4 sm:order-3 sm:col-span-2">
          <span class="text-muted">&copy; {{ new Date().getFullYear() }} Paul Diepold</span>
          <UButton
            color="neutral"
            variant="ghost"
            size="sm"
            :icon="isDark ? 'i-lucide-sun' : 'i-lucide-moon'"
            :aria-label="isDark ? 'Helles Design' : 'Dunkles Design'"
            @click="toggleColorMode"
          />
        </div>
      </UContainer>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, provide, ref } from 'vue';
import type { ButtonProps } from '@nuxt/ui';
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
  /** Knöpfe rechts neben dem Titel, auch am Handy in derselben Zeile. */
  headerLinks?: ButtonProps[];
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
        <div class="bg-blue-800 py-2 text-center">
          <Link :href="home.url()" class="text-lg font-semibold text-white no-underline"
            >Ewige Liste</Link
          >
        </div>

        <nav class="bg-blue-700">
          <!-- Beschriftungen erst ab `lg`, darunter passen sie nicht in eine Zeile. -->
          <UContainer class="max-w-md px-1 py-1 lg:max-w-4xl">
            <div class="flex items-center justify-around lg:gap-3">
              <UButton
                v-for="item in navItems"
                :key="item.to"
                :to="item.to"
                color="neutral"
                variant="ghost"
                size="xl"
                class="text-blue-100 hover:bg-white/15 hover:text-blue-200"
                :class="{
                  'rounded-none shadow-[inset_0_-3px_0_var(--ui-color-primary-400)]': item.active,
                }"
                :icon="item.icon"
                :aria-label="item.label"
                :aria-current="item.active ? 'page' : undefined"
              >
                <span class="hidden text-sm whitespace-nowrap lg:inline">{{ item.label }}</span>
              </UButton>
            </div>
          </UContainer>
        </nav>
      </header>

      <main class="flex flex-1 flex-col">
        <!-- Mit `fillHeight` unten nur 1rem Abstand, so viel wie unter dem schwebenden Button. -->
        <UContainer
          class="flex w-full max-w-4xl flex-1 flex-col pt-4 sm:pt-8"
          :class="fillHeight ? 'pb-4' : 'pb-4 sm:pb-8'"
        >
          <UPageHeader
            v-if="title"
            :title="title"
            :links="headerLinks"
            :ui="{
              root: 'relative border-none py-4 sm:py-5',
              wrapper: 'flex flex-row items-center justify-between gap-2',
              links: 'shrink-0',
              title: 'text-xl sm:text-2xl text-pretty font-semibold text-highlighted',
            }"
          />
          <UPageBody
            class="mt-2 flex-1 sm:mt-4"
            :class="fillHeight ? 'flex flex-col pb-0' : 'pb-8'"
          >
            <slot />
          </UPageBody>
        </UContainer>
      </main>

      <!--
        Schwebender Button: Die Leiste ohne Höhe klebt am unteren Bildschirmrand, aber nur so weit,
        wie Kopf und Inhalt reichen. Ab dem Footer bleibt sie am Inhaltsende stehen, der Button
        ragt nach oben in den Inhalt. Außerhalb von `UPageBody`, dessen `space-y-*` gäbe ihr sonst
        einen Außenabstand. Nur für Handy und Tablet; am Desktop bietet die Seite selbst einen
        Button mit Text an.
      -->
      <div v-if="$slots.floatingAction" class="sticky bottom-0 z-20 h-0 shrink-0 lg:hidden">
        <div class="absolute right-5 bottom-[calc(1rem+env(safe-area-inset-bottom))]">
          <slot name="floatingAction" />
        </div>
      </div>
    </div>

    <footer class="shrink-0 border-t border-default bg-elevated">
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
            <UButton
              color="neutral"
              variant="outline"
              size="sm"
              icon="i-lucide-log-out"
              label="Logout"
              @click="logout"
            />
          </template>
          <template v-else>
            <Link :href="register.url()" :class="footerLinkClass">Registrieren</Link>
            <UButton
              :to="login.url()"
              color="neutral"
              variant="outline"
              size="sm"
              icon="i-lucide-log-in"
              label="Login"
            />
          </template>
        </div>

        <nav aria-label="Rechtliches" class="flex flex-wrap items-center gap-x-2 gap-y-1">
          <template v-for="(item, index) in legalLinks" :key="item.href">
            <span v-if="index > 0" class="text-muted" aria-hidden="true">&middot;</span>
            <Link :href="item.href" :class="footerLinkClass">{{ item.label }}</Link>
          </template>
        </nav>

        <div class="flex items-center justify-between gap-4 sm:order-3 sm:col-span-2">
          <span class="text-muted">&copy; {{ new Date().getFullYear() }} Paul Diepold</span>
          <UButton
            color="neutral"
            variant="outline"
            size="sm"
            :icon="isDark ? 'i-lucide-sun' : 'i-lucide-moon'"
            :label="isDark ? 'Hell' : 'Dunkel'"
            @click="toggleColorMode"
          />
        </div>
      </UContainer>
    </footer>
  </div>
</template>

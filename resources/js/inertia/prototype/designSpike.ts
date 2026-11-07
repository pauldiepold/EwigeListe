/**
 * PROTOTYP #34, Wegwerf-Code: Design-Varianten (A bis D, Runde 2: E bis H), umschaltbar per `?design=a` und über den
 * DesignSwitcher. Die Wahl steht als `data-design` an `<html>` und übersteht Seitenwechsel per
 * localStorage. Dieser Branch wird nie gemergt, der Gewinner wird in #35 sauber neu umgesetzt.
 */
import { computed, ref } from 'vue';

export const designs = [
  { key: 'a', name: 'Feinschliff' },
  { key: 'b', name: 'Alte Liste' },
  { key: 'c', name: 'Spieltisch' },
  { key: 'd', name: 'Flach' },
  { key: 'e', name: 'Plus' },
  { key: 'f', name: 'Schmale Leiste' },
  { key: 'g', name: 'Aktionsleiste' },
  { key: 'h', name: 'Plus 2' },
] as const;

/** Runde 2: setzt Pauls Rückmeldung zu A bis D um (Kopf mit Bild, volle Breite, ohne Box). */
const SECOND_ROUND: readonly string[] = ['e', 'f', 'g', 'h'];

export type DesignKey = (typeof designs)[number]['key'];

const STORAGE_KEY = 'design-spike';

function isDesignKey(value: string | null): value is DesignKey {
  return designs.some((design) => design.key === value);
}

function initialDesign(): DesignKey {
  const fromUrl = new URLSearchParams(location.search).get('design');
  if (isDesignKey(fromUrl)) {
    localStorage.setItem(STORAGE_KEY, fromUrl);
    return fromUrl;
  }

  const stored = localStorage.getItem(STORAGE_KEY);
  return isDesignKey(stored) ? stored : 'a';
}

const current = ref<DesignKey>(initialDesign());
document.documentElement.dataset.design = current.value;

function setDesign(key: DesignKey): void {
  current.value = key;
  document.documentElement.dataset.design = key;
  localStorage.setItem(STORAGE_KEY, key);

  const url = new URL(location.href);
  url.searchParams.set('design', key);
  history.replaceState(history.state, '', url);
}

function cycle(step: 1 | -1): void {
  const index = designs.findIndex((design) => design.key === current.value);
  setDesign(designs[(index + step + designs.length) % designs.length].key);
}

export function useDesignSpike() {
  return {
    current,
    cycle,
    currentDesign: computed(() => designs.find((design) => design.key === current.value)!),
    isSecondRound: computed(() => SECOND_ROUND.includes(current.value)),
    tabsVariant: computed(() =>
      current.value === 'b' || current.value === 'd' ? ('link' as const) : ('pill' as const),
    ),
  };
}

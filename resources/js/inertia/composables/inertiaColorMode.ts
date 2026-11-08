import type { InjectionKey, Ref } from 'vue';

export type InertiaColorMode = 'light' | 'dark';

export const inertiaColorModeKey: InjectionKey<Ref<InertiaColorMode>> = Symbol('inertiaColorMode');

/** Der gemerkte Modus des Benutzers, sonst der des Geräts. */
export function resolveColorMode(): InertiaColorMode {
  const stored = localStorage.getItem('ui-color-mode');
  if (stored === 'dark' || stored === 'light') {
    return stored;
  }

  return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
}

import type { InjectionKey, Ref } from 'vue';

export type InertiaColorMode = 'light' | 'dark';

export const inertiaColorModeKey: InjectionKey<Ref<InertiaColorMode>> = Symbol('inertiaColorMode');

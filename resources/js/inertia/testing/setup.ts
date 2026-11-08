import { cleanup } from '@testing-library/vue';
import { afterEach, vi } from 'vitest';

// Ohne `globals: true` räumt Testing Library nicht selbst auf; Modals und Toasts teleportieren
// in `document.body` und blieben sonst für den nächsten Test stehen.
afterEach(() => {
  cleanup();
});

// jsdom kennt kein `matchMedia`; Layout und Breakpoints fragen es ab. Keine Media Query trifft zu,
// die Tests sehen also die Handy-Ansicht ohne Touch.
window.matchMedia = vi.fn((query: string) => ({
  matches: false,
  media: query,
  onchange: null,
  addEventListener: vi.fn(),
  removeEventListener: vi.fn(),
  addListener: vi.fn(),
  removeListener: vi.fn(),
  dispatchEvent: vi.fn(() => false),
}));

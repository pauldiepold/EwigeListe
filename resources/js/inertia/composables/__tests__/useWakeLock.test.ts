import { defineComponent, h } from 'vue';
import { render } from '@testing-library/vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { useWakeLock } from '@/composables/useWakeLock';

const Harness = defineComponent({
  setup() {
    useWakeLock();

    return () => h('div');
  },
});

function installWakeLock() {
  const release = vi.fn(() => Promise.resolve());
  const sentinel = { released: false, release };
  const request = vi.fn(() => Promise.resolve(sentinel));
  Object.defineProperty(navigator, 'wakeLock', { value: { request }, configurable: true });

  return { request, release, sentinel };
}

function setVisibility(state: 'visible' | 'hidden'): void {
  Object.defineProperty(document, 'visibilityState', { value: state, configurable: true });
  document.dispatchEvent(new Event('visibilitychange'));
}

afterEach(() => {
  Reflect.deleteProperty(navigator, 'wakeLock');
  setVisibility('visible');
});

describe('useWakeLock', () => {
  it('hält den Bildschirm an, solange die Seite offen ist, und gibt ihn danach frei', async () => {
    const { request, release } = installWakeLock();

    const { unmount } = render(Harness);
    await vi.waitFor(() => expect(request).toHaveBeenCalledWith('screen'));

    unmount();
    await vi.waitFor(() => expect(release).toHaveBeenCalledTimes(1));
  });

  it('fordert die Sperre erneut an, wenn die Seite wieder sichtbar wird', async () => {
    const { request, sentinel } = installWakeLock();
    render(Harness);
    await vi.waitFor(() => expect(request).toHaveBeenCalledTimes(1));

    // Der Browser gibt die Sperre frei, sobald die Seite verdeckt wird.
    sentinel.released = true;
    setVisibility('hidden');
    expect(request).toHaveBeenCalledTimes(1);

    setVisibility('visible');
    await vi.waitFor(() => expect(request).toHaveBeenCalledTimes(2));
  });

  it('tut nichts, wo der Browser keine Wake Lock kennt', () => {
    expect(() => render(Harness)).not.toThrow();
  });
});

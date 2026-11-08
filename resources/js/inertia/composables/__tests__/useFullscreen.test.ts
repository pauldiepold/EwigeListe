import { defineComponent, h } from 'vue';
import { render } from '@testing-library/vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import { useFullscreen } from '@/composables/useFullscreen';

let api: ReturnType<typeof useFullscreen>;

const Harness = defineComponent({
  setup() {
    api = useFullscreen();

    return () => h('div');
  },
});

function setFullscreenElement(element: Element | null): void {
  Object.defineProperty(document, 'fullscreenElement', { value: element, configurable: true });
  document.dispatchEvent(new Event('fullscreenchange'));
}

afterEach(() => {
  Reflect.deleteProperty(document, 'fullscreenEnabled');
  Reflect.deleteProperty(document, 'fullscreenElement');
  Reflect.deleteProperty(document, 'exitFullscreen');
  Reflect.deleteProperty(document.documentElement, 'requestFullscreen');
});

describe('useFullscreen', () => {
  it('ist ohne Fullscreen-API nicht verfügbar (iPhone)', () => {
    render(Harness);

    expect(api.isSupported.value).toBe(false);
  });

  it('schaltet Vollbild ein und wieder aus', async () => {
    const request = vi.fn(() => Promise.resolve());
    const exit = vi.fn(() => Promise.resolve());
    Object.defineProperty(document, 'fullscreenEnabled', { value: true, configurable: true });
    Object.defineProperty(document.documentElement, 'requestFullscreen', {
      value: request,
      configurable: true,
    });
    Object.defineProperty(document, 'exitFullscreen', { value: exit, configurable: true });
    render(Harness);

    expect(api.isSupported.value).toBe(true);
    expect(api.isFullscreen.value).toBe(false);

    await api.toggle();
    expect(request).toHaveBeenCalledTimes(1);

    setFullscreenElement(document.documentElement);
    expect(api.isFullscreen.value).toBe(true);

    await api.toggle();
    expect(exit).toHaveBeenCalledTimes(1);

    setFullscreenElement(null);
    expect(api.isFullscreen.value).toBe(false);
  });
});

import { screen } from '@testing-library/vue';
import { describe, expect, it, vi } from 'vitest';
import AppLayout from '@/Layouts/AppLayout.vue';
import { renderWithUi } from '@/testing/renderWithUi';

const page = vi.hoisted(() => ({
  url: '/',
  props: {
    auth: { user: { id: 1 }, player: { id: 5, fullName: 'Paul D.' } },
    navigation: { currentRoundId: 7 as number | null },
  },
  flash: {},
}));

vi.mock('@inertiajs/vue3', async (importOriginal) => ({
  ...(await importOriginal<typeof import('@inertiajs/vue3')>()),
  usePage: () => page,
}));

function activeNavLabels(url: string): string[] {
  page.url = url;
  renderWithUi(AppLayout, {});

  return screen
    .getAllByRole('link')
    .filter((link) => link.getAttribute('aria-current') === 'page')
    .map((link) => link.textContent?.trim() ?? '');
}

describe('AppLayout', () => {
  it.each(['/runde/7', '/runde/7/live'])(
    'markiert bei der neuesten Runde (%s) „Aktuelle Runde“',
    (url) => {
      expect(activeNavLabels(url)).toEqual(['Aktuelle Runde']);
    },
  );

  it('ordnet jede ältere geöffnete Runde dem Rundenarchiv zu', () => {
    expect(activeNavLabels('/runde/70')).toEqual(['Rundenarchiv']);
  });
});

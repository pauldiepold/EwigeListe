import { screen } from '@testing-library/vue';
import { describe, expect, it, vi } from 'vitest';
import LiveRoundButton from '@/Components/Rounds/LiveRoundButton.vue';
import { renderWithUi } from '@/testing/renderWithUi';

// Links fragen die aktuelle Adresse ab, die nur `createInertiaApp` anlegt.
vi.mock('@inertiajs/vue3', async (importOriginal) => ({
  ...(await importOriginal<typeof import('@inertiajs/vue3')>()),
  usePage: () => ({ url: '/runde/7', props: {} }),
}));

describe('LiveRoundButton', () => {
  it('lädt Spieler des aktuellen Spiels an den Tisch', () => {
    renderWithUi(LiveRoundButton, { roundId: 7, viewerIsActive: true });

    const link = screen.getByRole('link', { name: 'Live spielen' });
    expect(link.getAttribute('href')).toBe('/runde/7/live');
  });

  it('lässt alle anderen zuschauen', () => {
    renderWithUi(LiveRoundButton, { roundId: 7, viewerIsActive: false });

    const link = screen.getByRole('link', { name: 'Zuschauen' });
    expect(link.getAttribute('href')).toBe('/runde/7/live');
    expect(screen.queryByRole('link', { name: 'Live spielen' })).toBeNull();
  });
});

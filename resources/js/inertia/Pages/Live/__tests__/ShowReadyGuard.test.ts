import { router } from '@inertiajs/vue3';
import { screen } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import Show from '@/Pages/Live/Show.vue';
import { echo, resetLiveEcho } from '@/testing/liveEcho';
import { livePlayer } from '@/testing/live';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveEchoMember } from '@/composables/useLiveEcho';

vi.mock('@/composables/useLiveEcho', async () =>
  (await import('@/testing/liveEcho')).liveEchoModule(),
);
vi.mock('@/composables/liveRequest', () => ({ postLive: vi.fn() }));

// Die Übersicht meldet „bereit“ hier immer, auch bei gesperrtem Button: Die Seite muss sich selbst schützen.
vi.mock('@/Components/Live/LivePlayerOverview.vue', async () => {
  const { defineComponent, h } = await import('vue');

  return {
    default: defineComponent({
      emits: ['ready'],
      setup:
        (_, { emit }) =>
        () =>
          h('button', { onClick: () => emit('ready') }, 'Ungeschützt bereit'),
    }),
  };
});

vi.mock('@inertiajs/vue3', async (importOriginal) => {
  const { defineComponent } = await import('vue');

  return {
    ...(await importOriginal<typeof import('@inertiajs/vue3')>()),
    Head: defineComponent({ render: () => null }),
    usePage: () => ({ url: '/runde/7/live', props: {} }),
  };
});

const players = [livePlayer(11, 'Bernd'), livePlayer(12, 'Clara')];

function member(id: number, name: string): LiveEchoMember {
  return { id, name: `${name} Test`, avatar_path: null };
}

function renderShow() {
  return renderWithUi(Show, {
    round: {
      id: 7,
      path: '/runde/7',
      gamesCount: 0,
      liveRoundId: 5,
      rules: {
        schweinchen: true,
        fuchsSticht: true,
        schweinchenTrumpfsolo: false,
        koenigsSolo: true,
        karlchen: true,
        karlchenFangen: false,
      },
      viewerPlayerId: 12,
      players,
    },
    liveGame: null,
    lastLiveGame: null,
    ich: null,
  });
}

beforeEach(() => {
  resetLiveEcho();
  vi.spyOn(router, 'reload').mockImplementation(() => undefined);
});

describe('Live-Seite: Schutz der Bereit-Meldung', () => {
  it('flüstert „bereit“ nicht, solange ein Spieler des Spiels fehlt', async () => {
    renderShow();
    echo.onlineMembers.value = [member(12, 'Clara')];

    await userEvent.click(await screen.findByRole('button', { name: 'Ungeschützt bereit' }));

    expect(echo.sendReady).not.toHaveBeenCalled();
  });

  it('flüstert „bereit“, sobald alle online sind', async () => {
    renderShow();
    echo.onlineMembers.value = [member(11, 'Bernd'), member(12, 'Clara')];

    await userEvent.click(await screen.findByRole('button', { name: 'Ungeschützt bereit' }));

    expect(echo.sendReady).toHaveBeenCalledWith(12);
  });
});

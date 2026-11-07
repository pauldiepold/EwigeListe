import { screen } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import EnterGameModal from '@/Components/Rounds/EnterGameModal.vue';
import { fakeVisits } from '@/testing/fakeVisits';
import { renderWithUi } from '@/testing/renderWithUi';

const activePlayers = [
  { id: 11, name: 'Anna', avatarPath: null },
  { id: 12, name: 'Bernd', avatarPath: null },
  { id: 13, name: 'Clara', avatarPath: null },
  { id: 14, name: 'Dieter', avatarPath: null },
];

function renderModal() {
  return renderWithUi(EnterGameModal, {
    roundId: 7,
    lastGameId: 100,
    activePlayers,
    viewerIsActive: true,
  });
}

describe('EnterGameModal', () => {
  it('trägt die gewählten Gewinner mit ihren Punkten ein', async () => {
    const post = fakeVisits('post');
    const user = userEvent.setup();
    renderModal();

    await user.click(screen.getByRole('button', { name: 'Spiel eintragen' }));
    await user.click(await screen.findByRole('button', { name: /Anna/ }));
    await user.click(screen.getByRole('button', { name: /Clara/ }));
    await user.click(screen.getByRole('button', { name: 'Mehr Punkte' }));
    await user.click(screen.getByRole('button', { name: 'Mehr Punkte' }));
    await user.click(screen.getByRole('button', { name: 'Speichern' }));

    expect(post).toHaveBeenCalledWith(
      '/rounds/7/games',
      { winners: [11, 13], points: 2, misplayed: false },
      expect.anything(),
    );
  });

  it('schickt ohne Gewinner nichts ab', async () => {
    const post = fakeVisits('post');
    const user = userEvent.setup();
    renderModal();

    await user.click(screen.getByRole('button', { name: 'Spiel eintragen' }));
    await user.click(await screen.findByRole('button', { name: 'Speichern' }));

    expect(post).not.toHaveBeenCalled();
    expect(screen.getByRole('alert').textContent).toContain('mindestens eine');
  });

  it('schickt bei „Falsch bedient“ nur mit genau drei Gewinnern ab', async () => {
    const post = fakeVisits('post');
    const user = userEvent.setup();
    renderModal();

    await user.click(screen.getByRole('button', { name: 'Spiel eintragen' }));
    await user.click(await screen.findByRole('button', { name: /Anna/ }));
    await user.click(screen.getByRole('switch', { name: 'Falsch bedient' }));
    await user.click(screen.getByRole('button', { name: 'Speichern' }));

    expect(post).not.toHaveBeenCalled();
    expect(screen.getByRole('alert').textContent).toContain('genau drei');
  });

  it('öffnet nach einem gescheiterten Speichern wieder mit Eingabe und Fehler', async () => {
    fakeVisits('post', { errors: { points: 'Die Punkte sind ungültig.' } });
    const user = userEvent.setup();
    renderModal();

    await user.click(screen.getByRole('button', { name: 'Spiel eintragen' }));
    await user.click(await screen.findByRole('button', { name: /Bernd/ }));
    await user.click(screen.getByRole('button', { name: 'Speichern' }));

    await screen.findByText('Die Punkte sind ungültig.');
    expect(screen.getByRole('button', { name: /Bernd/ }).getAttribute('aria-pressed')).toBe('true');
  });

  it('schließt mit Hinweis, wenn ein anderes Handy inzwischen ein Spiel einträgt', async () => {
    const user = userEvent.setup();
    const { rerender } = renderModal();

    await user.click(screen.getByRole('button', { name: 'Spiel eintragen' }));
    await screen.findByRole('dialog');
    await rerender({ lastGameId: 101 });

    await screen.findByText('Inzwischen wurde ein Spiel eingetragen');
    expect(screen.queryByRole('dialog')).toBeNull();
  });
});

import { screen } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import LiveReservations from '@/Components/Live/LiveReservations.vue';
import { renderWithUi } from '@/testing/renderWithUi';

/** Was die Engine einem Spieler ohne Armut, Hochzeit und Schmeißen anbietet. */
const SOLOS_ONLY = [
  'Fleischlos',
  'Bubensolo',
  'Damensolo',
  'Königssolo',
  'Trumpfsolo',
  'Farbsolo Herz',
  'Farbsolo Pik',
  'Farbsolo Kreuz',
];

function renderReservations(props: Record<string, unknown> = {}) {
  return renderWithUi(LiveReservations, {
    possible: SOLOS_ONLY,
    koenigsSolo: true,
    isMyTurn: true,
    decided: false,
    ...props,
  });
}

function isDisabled(name: string): boolean {
  return (screen.getByRole('button', { name }) as HTMLButtonElement).disabled;
}

describe('LiveReservations', () => {
  it('lässt mich wählen, wenn ich dran bin, und meldet den Vorbehalt', async () => {
    const onChoose = vi.fn();
    renderReservations({ onChoose });

    expect(screen.getByText('Wähle deinen Vorbehalt:')).toBeTruthy();
    await userEvent.click(screen.getByRole('button', { name: 'Gesund' }));

    expect(onChoose).toHaveBeenCalledWith('Gesund');
  });

  it('zeigt die möglichen Vorbehalte schon vorher, aber gesperrt', async () => {
    const onChoose = vi.fn();
    renderReservations({ isMyTurn: false, onChoose });

    expect(screen.getByText('Mögliche Vorbehalte:')).toBeTruthy();
    for (const name of ['Gesund', 'Fleischlos', 'Bubensolo', 'Damensolo', 'Königssolo']) {
      expect(isDisabled(name)).toBe(true);
    }
    await userEvent.click(screen.getByRole('button', { name: 'Gesund' }));
    expect(onChoose).not.toHaveBeenCalled();
  });

  it('bietet Armut, Hochzeit, Stille Hochzeit und Schmeißen an, wenn die Hand sie erlaubt', () => {
    renderReservations({
      possible: ['Armut', 'Hochzeit', 'Stille Hochzeit', 'Schmeißen', ...SOLOS_ONLY],
    });

    for (const name of ['Armut', 'Hochzeit', 'Stille Hochzeit', 'Schmeißen']) {
      expect(screen.getByRole('button', { name })).toBeTruthy();
    }
  });

  it('bietet ohne Armut, Hochzeit und Schmeißen nur Gesund und die Soli an', () => {
    renderReservations();

    for (const name of ['Armut', 'Hochzeit', 'Stille Hochzeit', 'Schmeißen']) {
      expect(screen.queryByRole('button', { name })).toBeNull();
    }
  });

  it('bietet bei einer Hochzeit kein „Gesund“ an', () => {
    renderReservations({ possible: ['Hochzeit', 'Stille Hochzeit', ...SOLOS_ONLY] });

    expect(screen.queryByRole('button', { name: 'Gesund' })).toBeNull();
    expect(screen.getByRole('button', { name: 'Hochzeit' })).toBeTruthy();
  });

  it('bietet das Königssolo nur an, wenn die Regeln es erlauben', async () => {
    const { rerender } = renderReservations({ koenigsSolo: false });

    expect(screen.queryByRole('button', { name: 'Königssolo' })).toBeNull();
    expect(screen.getByRole('button', { name: 'Damensolo' })).toBeTruthy();

    await rerender({ koenigsSolo: true });

    expect(screen.getByRole('button', { name: 'Königssolo' })).toBeTruthy();
  });

  it('versteckt die Farbsoli hinter dem Button „Farbsolo“', async () => {
    const onChoose = vi.fn();
    renderReservations({ onChoose });

    for (const name of ['Trumpfsolo', 'Farbsolo Herz', 'Farbsolo Pik', 'Farbsolo Kreuz']) {
      expect(screen.queryByRole('button', { name })).toBeNull();
    }

    await userEvent.click(screen.getByRole('button', { name: 'Farbsolo' }));

    expect(screen.queryByRole('button', { name: 'Farbsolo' })).toBeNull();
    await userEvent.click(screen.getByRole('button', { name: 'Farbsolo Pik' }));
    expect(onChoose).toHaveBeenCalledWith('Farbsolo Pik');
    expect(screen.getByRole('button', { name: 'Trumpfsolo' })).toBeTruthy();
    expect(screen.getByRole('button', { name: 'Farbsolo Herz' })).toBeTruthy();
    expect(screen.getByRole('button', { name: 'Farbsolo Kreuz' })).toBeTruthy();
  });

  it('lässt mich die Farbsoli auch ansehen, solange ich nicht dran bin', async () => {
    renderReservations({ isMyTurn: false });

    await userEvent.click(screen.getByRole('button', { name: 'Farbsolo' }));

    expect(isDisabled('Trumpfsolo')).toBe(true);
  });

  it('sperrt alle Buttons, solange meine Wahl unterwegs ist', () => {
    renderReservations({ busy: true });

    expect(isDisabled('Gesund')).toBe(true);
    expect(isDisabled('Fleischlos')).toBe(true);
  });

  it('bittet nach meiner Wahl zu warten und zeigt keine Buttons mehr', () => {
    renderReservations({ decided: true });

    expect(screen.getByText('Bitte warte, bis alle ihren Vorbehalt bestimmt haben.')).toBeTruthy();
    expect(screen.queryByRole('button')).toBeNull();
  });
});

import { screen } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import LivePoverty from '@/Components/Live/LivePoverty.vue';
import { renderWithUi } from '@/testing/renderWithUi';

function renderPoverty(props: Record<string, unknown> = {}) {
  return renderWithUi(LivePoverty, {
    phase: 3,
    isMyTurn: true,
    activePlayerName: 'Bernd',
    canSubmit: false,
    trumpLeft: false,
    ...props,
  });
}

describe('LivePoverty: Karten abgeben (Phase 3)', () => {
  it('fordert die Armut auf, drei Karten zu wählen, ohne Button vor der dritten Karte', () => {
    renderPoverty();

    expect(screen.getByText('Wähle aus, welche Karten du abgeben möchtest!')).toBeTruthy();
    expect(screen.queryByRole('button', { name: 'Karten abgeben' })).toBeNull();
  });

  it('bietet bei drei gewählten Karten das Abgeben an', async () => {
    const onGive = vi.fn();
    renderPoverty({ canSubmit: true, onGive });

    await userEvent.click(screen.getByRole('button', { name: 'Karten abgeben' }));

    expect(onGive).toHaveBeenCalledTimes(1);
  });

  it('warnt, wenn noch Trumpf auf der Hand bleibt', () => {
    renderPoverty({ canSubmit: true, trumpLeft: true });

    expect(screen.getByText('Du musst all deinen Trumpf abgeben!')).toBeTruthy();
  });

  it('zeigt die Warnung nicht ohne Trumpf auf der Hand', () => {
    renderPoverty({ canSubmit: true });

    expect(screen.queryByText('Du musst all deinen Trumpf abgeben!')).toBeNull();
  });

  it('sperrt das Abgeben, solange die Aktion unterwegs ist', () => {
    renderPoverty({ canSubmit: true, busy: true });

    expect(
      (screen.getByRole('button', { name: 'Karten abgeben' }) as HTMLButtonElement).disabled,
    ).toBe(true);
  });

  it('sagt den anderen, dass die Armut ihre Karten wählt', () => {
    renderPoverty({ isMyTurn: false });

    expect(screen.getByText('Die Armut wählt ihre Karten.')).toBeTruthy();
    expect(screen.queryByRole('button')).toBeNull();
  });
});

describe('LivePoverty: Armut mitnehmen (Phase 32)', () => {
  it('fragt, wer dran ist, und meldet Ja und Nein', async () => {
    const onAnswer = vi.fn();
    renderPoverty({ phase: 32, onAnswer });

    expect(screen.getByText('Willst du die Armut mitnehmen?')).toBeTruthy();
    await userEvent.click(screen.getByRole('button', { name: 'Ja!' }));
    await userEvent.click(screen.getByRole('button', { name: 'Nein!' }));

    expect(onAnswer).toHaveBeenNthCalledWith(1, true);
    expect(onAnswer).toHaveBeenNthCalledWith(2, false);
  });

  it('sperrt die Antwort, solange sie unterwegs ist', () => {
    renderPoverty({ phase: 32, busy: true });

    expect((screen.getByRole('button', { name: 'Ja!' }) as HTMLButtonElement).disabled).toBe(true);
    expect((screen.getByRole('button', { name: 'Nein!' }) as HTMLButtonElement).disabled).toBe(
      true,
    );
  });

  it('zeigt den anderen, wer überlegt', () => {
    renderPoverty({ phase: 32, isMyTurn: false, activePlayerName: 'Clara' });

    expect(screen.getByText('Clara überlegt …')).toBeTruthy();
    expect(screen.queryByRole('button')).toBeNull();
  });
});

describe('LivePoverty: Karten zurückgeben (Phase 33)', () => {
  it('fordert den Mitnehmer auf, drei Karten zurückzugeben', async () => {
    renderPoverty({ phase: 33 });

    expect(screen.getByText('Wähle aus, welche Karten du zurückgeben möchtest!')).toBeTruthy();
    expect(screen.queryByRole('button', { name: 'Karten zurückgeben' })).toBeNull();
  });

  it('bietet bei drei gewählten Karten das Zurückgeben an', async () => {
    const onGiveBack = vi.fn();
    renderPoverty({ phase: 33, canSubmit: true, onGiveBack });

    await userEvent.click(screen.getByRole('button', { name: 'Karten zurückgeben' }));

    expect(onGiveBack).toHaveBeenCalledTimes(1);
  });

  it('zeigt den anderen, wer Karten zurückgibt', () => {
    renderPoverty({ phase: 33, isMyTurn: false, activePlayerName: 'Clara' });

    expect(screen.getByText('Clara gibt Karten zurück …')).toBeTruthy();
  });
});

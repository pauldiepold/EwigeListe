import { screen } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { afterEach, describe, expect, it, vi } from 'vitest';
import LiveToolbar from '@/Components/Live/LiveToolbar.vue';
import { renderWithUi } from '@/testing/renderWithUi';
import type { LiveRules } from '@/Components/Live/liveRules';

// Links fragen die aktuelle Adresse ab, die nur `createInertiaApp` anlegt.
vi.mock('@inertiajs/vue3', async (importOriginal) => ({
  ...(await importOriginal<typeof import('@inertiajs/vue3')>()),
  usePage: () => ({ url: '/runde/7/live', props: {} }),
}));

const allRules: LiveRules = {
  schweinchen: true,
  fuchsSticht: true,
  schweinchenTrumpfsolo: false,
  koenigsSolo: true,
  karlchen: true,
  karlchenFangen: false,
};

function renderToolbar(rules: LiveRules = allRules) {
  return renderWithUi(LiveToolbar, { backPath: '/runde/7', rules });
}

afterEach(() => {
  Reflect.deleteProperty(document, 'fullscreenEnabled');
});

describe('LiveToolbar', () => {
  it('führt zurück zur Runde', () => {
    renderToolbar();

    const back = screen.getByRole('link', { name: 'Zurück zur Runde' });
    expect(back.getAttribute('href')).toBe('/runde/7');
  });

  it('zeigt die Regeln der Runde im Info-Bereich', async () => {
    const user = userEvent.setup();
    renderToolbar();

    await user.click(screen.getByRole('button', { name: 'Info' }));

    const dialog = await screen.findByRole('dialog');
    expect(dialog.textContent).toContain('Regeln dieser Runde');
    const rule = (name: string) => screen.getByText(name).closest('li')!.textContent;
    expect(rule('Super-Schwein')).toContain('an');
    expect(rule('Fuchs muss stechen')).toContain('an');
    expect(rule('Super-Schwein auch im Trumpfsolo')).toContain('aus');
    expect(rule('Karlchen')).toContain('an');
    expect(rule('Karlchen kann gefangen werden')).toContain('aus');
  });

  it('verbirgt abhängige Regeln, wenn die übergeordnete Regel aus ist', async () => {
    const user = userEvent.setup();
    renderToolbar({ ...allRules, schweinchen: false, karlchen: false });

    await user.click(screen.getByRole('button', { name: 'Info' }));
    await screen.findByRole('dialog');

    expect(screen.queryByText('Fuchs muss stechen')).toBeNull();
    expect(screen.queryByText('Super-Schwein auch im Trumpfsolo')).toBeNull();
    expect(screen.queryByText('Karlchen kann gefangen werden')).toBeNull();
  });

  it('schaltet das Meldungsprotokoll um', async () => {
    const user = userEvent.setup();
    const onToggle = vi.fn();
    const { rerender } = renderWithUi(LiveToolbar, {
      backPath: '/runde/7',
      rules: allRules,
      messagesOpen: false,
      'onUpdate:messagesOpen': onToggle,
    });

    const button = screen.getByRole('button', { name: 'Meldungen' });
    expect(button.getAttribute('aria-pressed')).toBe('false');

    await user.click(button);
    expect(onToggle).toHaveBeenLastCalledWith(true);

    await rerender({ messagesOpen: true });
    expect(screen.getByRole('button', { name: 'Meldungen' }).getAttribute('aria-pressed')).toBe(
      'true',
    );

    await user.click(screen.getByRole('button', { name: 'Meldungen' }));
    expect(onToggle).toHaveBeenLastCalledWith(false);
  });

  it('bietet Vollbild nur an, wo der Browser es kennt', () => {
    renderToolbar();
    expect(screen.queryByRole('button', { name: 'Vollbild' })).toBeNull();
  });

  it('bietet Vollbild an, wenn der Browser es kennt', async () => {
    Object.defineProperty(document, 'fullscreenEnabled', { value: true, configurable: true });
    renderToolbar();

    expect(await screen.findByRole('button', { name: 'Vollbild' })).toBeTruthy();
  });
});

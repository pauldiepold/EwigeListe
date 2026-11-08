import { screen } from '@testing-library/vue';
import userEvent from '@testing-library/user-event';
import { describe, expect, it, vi } from 'vitest';
import LiveRulesConfirmModal from '@/Components/Live/LiveRulesConfirmModal.vue';
import LiveRulesFields from '@/Components/Live/LiveRulesFields.vue';
import { activeLiveRuleLabels, defaultLiveRules } from '@/Components/Live/liveRules';
import { renderWithUi } from '@/testing/renderWithUi';

describe('activeLiveRuleLabels', () => {
  it('nennt beim Standard alle Regeln außer Super-Schwein im Trumpfsolo', () => {
    expect(activeLiveRuleLabels(defaultLiveRules())).not.toContain(
      'Super-Schwein auch im Trumpfsolo',
    );
    expect(activeLiveRuleLabels(defaultLiveRules())).toHaveLength(5);
  });

  it('lässt abhängige Regeln weg, wenn die übergeordnete aus ist', () => {
    const labels = activeLiveRuleLabels({
      ...defaultLiveRules(),
      schweinchen: false,
      karlchen: false,
    });

    expect(labels).toEqual(['Königssolo']);
  });
});

describe('LiveRulesFields', () => {
  it('zeigt abhängige Schalter nur bei aktiver übergeordneter Regel', async () => {
    const user = userEvent.setup();
    renderWithUi(LiveRulesFields, { modelValue: defaultLiveRules() });

    expect(screen.getByRole('switch', { name: /Fuchs muss stechen/ })).toBeTruthy();
    expect(screen.getByRole('switch', { name: /Karlchen kann gefangen werden/ })).toBeTruthy();

    await user.click(screen.getByRole('switch', { name: 'Super-Schwein' }));

    expect(screen.queryByRole('switch', { name: /Fuchs muss stechen/ })).toBeNull();
    expect(screen.queryByRole('switch', { name: /auch im Trumpfsolo/ })).toBeNull();
    expect(screen.getByRole('switch', { name: /Karlchen kann gefangen werden/ })).toBeTruthy();
  });
});

describe('LiveRulesConfirmModal', () => {
  function renderModal(props: Record<string, unknown> = {}) {
    return renderWithUi(LiveRulesConfirmModal, {
      open: true,
      rules: { ...defaultLiveRules(), koenigsSolo: false },
      ...props,
    });
  }

  it('listet aktive und deaktivierte Regeln', async () => {
    renderModal();

    expect(await screen.findByText('Mit diesen Regeln spielen?')).toBeTruthy();
    expect(
      screen.getByRole('listitem', { name: 'Karlchen kann gefangen werden: an' }),
    ).toBeTruthy();
    expect(screen.getByRole('listitem', { name: 'Königssolo: aus' })).toBeTruthy();
  });

  it('lässt abhängige Regeln weg, wenn die übergeordnete aus ist', async () => {
    renderModal({ rules: { ...defaultLiveRules(), karlchen: false } });

    expect(await screen.findByRole('listitem', { name: 'Karlchen: aus' })).toBeTruthy();
    expect(screen.queryByText('Karlchen kann gefangen werden')).toBeNull();
  });

  it('meldet „Runde erstellen“ als Bestätigung', async () => {
    const onConfirm = vi.fn();
    const user = userEvent.setup();
    renderModal({ onConfirm });

    await user.click(await screen.findByRole('button', { name: 'Runde erstellen' }));

    expect(onConfirm).toHaveBeenCalledOnce();
  });

  it('schließt mit „Regeln ändern“ ohne Bestätigung', async () => {
    const onConfirm = vi.fn();
    const onUpdateOpen = vi.fn();
    const user = userEvent.setup();
    renderModal({ onConfirm, 'onUpdate:open': onUpdateOpen });

    await user.click(await screen.findByRole('button', { name: 'Regeln ändern' }));

    expect(onUpdateOpen).toHaveBeenCalledWith(false);
    expect(onConfirm).not.toHaveBeenCalled();
  });
});

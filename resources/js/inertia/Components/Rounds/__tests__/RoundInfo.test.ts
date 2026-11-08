import { screen } from '@testing-library/vue';
import { describe, expect, it } from 'vitest';
import { defaultLiveRules } from '@/Components/Live/liveRules';
import RoundInfo from '@/Components/Rounds/RoundInfo.vue';
import { renderWithUi } from '@/testing/renderWithUi';
import type { RoundInfo as RoundInfoData } from '@/types/round-show';

const info: RoundInfoData = {
  gamesCount: 0,
  averageGameMinutes: null,
  startedBy: 'Anna',
  startedAt: 'heute 14:00',
  lastGameBy: null,
  lastGameAt: null,
  rules: null,
};

describe('RoundInfo', () => {
  it('zeigt bei einer Offline-Runde keine Regeln', () => {
    renderWithUi(RoundInfo, { info });

    expect(screen.queryByText(/Regeln/)).toBeNull();
  });

  it('zeigt die aktiven Regeln einer Live-Runde', () => {
    renderWithUi(RoundInfo, {
      info: { ...info, rules: { ...defaultLiveRules(), koenigsSolo: false, karlchen: false } },
    });

    const rules = screen.getByText(/Regeln/).closest('p');
    expect(rules?.textContent).toContain('Super-Schwein');
    expect(rules?.textContent).toContain('Fuchs muss stechen');
    expect(rules?.textContent).not.toContain('Königssolo');
    expect(rules?.textContent).not.toContain('Karlchen');
  });

  it('nennt eine Live-Runde ohne Regeln „ohne Sonderregeln“', () => {
    const rules = {
      schweinchen: false,
      fuchsSticht: false,
      schweinchenTrumpfsolo: false,
      koenigsSolo: false,
      karlchen: false,
      karlchenFangen: false,
    };
    renderWithUi(RoundInfo, { info: { ...info, rules } });

    expect(screen.getByText(/ohne Sonderregeln/)).toBeTruthy();
  });
});

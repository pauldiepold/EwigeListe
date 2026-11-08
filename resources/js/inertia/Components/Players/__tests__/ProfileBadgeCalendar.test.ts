import { screen } from '@testing-library/vue';
import { describe, expect, it } from 'vitest';
import ProfileBadgeCalendar from '@/Components/Players/ProfileBadgeCalendar.vue';
import { renderWithUi } from '@/testing/renderWithUi';

const anna = (value: number) => ({ firstName: 'Anna', value, path: null });

describe('ProfileBadgeCalendar', () => {
  it('zeigt jeden Wert im richtigen Monat und zählt die Abzeichen pro Jahr', () => {
    renderWithUi(ProfileBadgeCalendar, {
      years: [
        {
          year: 2026,
          months: [
            { month: 10, points: anna(87), games: anna(41) },
            { month: 3, points: anna(64), games: null },
          ],
        },
      ],
    });

    expect(screen.getByTitle('Meiste Punkte im Oktober 2026: 87').textContent).toContain('87');
    expect(screen.getByTitle('Meiste Spiele im Oktober 2026: 41').textContent).toContain('41');
    expect(screen.getByTitle('Meiste Punkte im März 2026: 64').textContent).toContain('64');
    expect(screen.queryByTitle(/Meiste Spiele im März/)).toBeNull();
    expect(screen.getByText('2× Punkte · 1× Spiele')).toBeTruthy();
  });
});

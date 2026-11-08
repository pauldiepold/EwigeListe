import type { BadgeType } from '@/types/groups-show';

/** Darstellung der Abzeichen auf Profil- und Listenseite. */
export type BadgeTypeStyle = {
  key: BadgeType;
  label: string;
  shortLabel: string;
  icon: string;
  iconClass: string;
  /** Getönte Zelle eines gewonnenen Monats. */
  cellClass: string;
};

/** Farbe nur als Akzent, die Flächen bleiben neutral: Gold für Punkte, Markenblau für Spiele. */
export const BADGE_STYLES: Record<BadgeType, BadgeTypeStyle> = {
  points: {
    key: 'points',
    label: 'Meiste Punkte',
    shortLabel: 'Punkte',
    icon: 'i-lucide-crown',
    iconClass: 'text-amber-600 dark:text-amber-400',
    cellClass: 'bg-amber-100 text-amber-800 dark:bg-amber-400/15 dark:text-amber-300',
  },
  games: {
    key: 'games',
    label: 'Meiste Spiele',
    shortLabel: 'Spiele',
    icon: 'i-lucide-spade',
    iconClass: 'text-blue-700 dark:text-blue-300',
    cellClass: 'bg-blue-100 text-blue-800 dark:bg-blue-400/15 dark:text-blue-200',
  },
};

/** In Anzeigereihenfolge. */
export const BADGE_TYPES: BadgeTypeStyle[] = [BADGE_STYLES.points, BADGE_STYLES.games];

const monthFormat = new Intl.DateTimeFormat('de-DE', { month: 'long' });

/** Monatsname eines Abzeichens; `month` zählt ab 1. */
export function monthName(month: number): string {
  return monthFormat.format(new Date(2000, month - 1, 1));
}

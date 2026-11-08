import type { LiveRules } from '@/types/live';

export type LiveRuleDefinition = {
  key: keyof LiveRules;
  label: string;
  description: string;
  /** Gilt nur, solange diese Regel an ist. */
  requires?: keyof LiveRules;
};

/** Die Regeln einer Live-Runde mit Erklärung, in der Reihenfolge der Anzeige. */
export const LIVE_RULES: LiveRuleDefinition[] = [
  {
    key: 'schweinchen',
    label: 'Super-Schwein',
    description:
      'Der erste Fuchs wird als Fuchs gespielt, der zweite ist als Super-Schwein die höchste Karte im Spiel.',
  },
  {
    key: 'fuchsSticht',
    label: 'Super-Schwein: Fuchs muss stechen',
    description:
      'Der erste Fuchs muss den Stich gewinnen, damit der zweite zum Super-Schwein wird.',
    requires: 'schweinchen',
  },
  {
    key: 'schweinchenTrumpfsolo',
    label: 'Super-Schwein: auch im Trumpfsolo',
    description: 'Farbsolo-Karo, nicht in anderen Farbsoli.',
    requires: 'schweinchen',
  },
  {
    key: 'koenigsSolo',
    label: 'Königssolo',
    description: 'Nur die Könige sind Trumpf.',
  },
  {
    key: 'karlchen',
    label: 'Karlchen',
    description: 'Sticht ein Kreuz Bube den letzten Stich, gibt dies einen Extrapunkt.',
  },
  {
    key: 'karlchenFangen',
    label: 'Karlchen kann gefangen werden',
    description: 'Ein gefangener Kreuz Bube im letzten Stich gibt einen Extrapunkt.',
    requires: 'karlchen',
  },
];

/** Die Regeln, die in dieser Runde überhaupt eine Rolle spielen: abhängige nur bei aktiver übergeordneter. */
export function visibleRules(rules: LiveRules): LiveRuleDefinition[] {
  return LIVE_RULES.filter((rule) => rule.requires === undefined || rules[rule.requires]);
}

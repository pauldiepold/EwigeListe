/** Die Regeln einer Live-Runde; die Schlüssel entsprechen den Spalten von `live_rounds`. */
export type LiveRuleKey =
  | 'schweinchen'
  | 'fuchsSticht'
  | 'schweinchenTrumpfsolo'
  | 'koenigsSolo'
  | 'karlchen'
  | 'karlchenFangen';

export type LiveRules = Record<LiveRuleKey, boolean>;

export type LiveRuleDefinition = {
  key: LiveRuleKey;
  label: string;
  description: string;
  /** Übergeordnete Regel: Ist sie aus, gibt es diese Regel nicht. */
  parent?: LiveRuleKey;
};

/** In Anzeigereihenfolge; abhängige Regeln stehen direkt unter ihrer übergeordneten. */
export const liveRuleDefinitions: LiveRuleDefinition[] = [
  {
    key: 'schweinchen',
    label: 'Super-Schwein',
    description:
      'Der erste Fuchs wird als Fuchs gespielt, der zweite ist als Super-Schwein die höchste Karte im Spiel',
  },
  {
    key: 'fuchsSticht',
    label: 'Fuchs muss stechen',
    description: 'Der erste Fuchs muss den Stich gewinnen, damit der zweite zum Super-Schwein wird',
    parent: 'schweinchen',
  },
  {
    key: 'schweinchenTrumpfsolo',
    label: 'Super-Schwein auch im Trumpfsolo',
    description: 'Farbsolo-Karo, nicht in anderen Farbsoli',
    parent: 'schweinchen',
  },
  {
    key: 'koenigsSolo',
    label: 'Königssolo',
    description: 'Nur die Könige sind Trumpf',
  },
  {
    key: 'karlchen',
    label: 'Karlchen',
    description: 'Sticht ein Kreuz Bube den letzten Stich, gibt dies einen Extrapunkt',
  },
  {
    key: 'karlchenFangen',
    label: 'Karlchen kann gefangen werden',
    description: 'Ein gefangener Kreuz Bube im letzten Stich gibt einen Extrapunkt',
    parent: 'karlchen',
  },
];

/** Der übliche Standard: alle Regeln an. */
export function defaultLiveRules(): LiveRules {
  return {
    schweinchen: true,
    fuchsSticht: true,
    schweinchenTrumpfsolo: true,
    koenigsSolo: true,
    karlchen: true,
    karlchenFangen: true,
  };
}

/** Eine Regel gilt nur, wenn sie an ist und ihre übergeordnete Regel (falls es eine gibt) auch. */
export function isLiveRuleActive(rules: LiveRules, definition: LiveRuleDefinition): boolean {
  return rules[definition.key] && (definition.parent === undefined || rules[definition.parent]);
}

export function activeLiveRuleLabels(rules: LiveRules): string[] {
  return liveRuleDefinitions
    .filter((definition) => isLiveRuleActive(rules, definition))
    .map((definition) => definition.label);
}

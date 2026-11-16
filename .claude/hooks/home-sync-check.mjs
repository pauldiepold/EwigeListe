// Prüft, dass die Dateien aus dem Home Repo unverändert sind (Hashes in `.claude/home-sync.json`).
// Läuft in der CI: `node .claude/hooks/home-sync-check.mjs`. Regeln dazu: `.claude/rules/home.md`.
import { createHash } from 'node:crypto';
import { existsSync, readFileSync } from 'node:fs';

const { dateien } = JSON.parse(readFileSync('.claude/home-sync.json', 'utf8'));
const fehler = [];

for (const [datei, soll] of Object.entries(dateien)) {
  if (!existsSync(datei)) {
    fehler.push(`fehlt:    ${datei}`);
    continue;
  }
  const inhalt = readFileSync(datei, 'utf8').replace(/\r\n/g, '\n');
  if (createHash('sha256').update(inhalt).digest('hex') !== soll) {
    fehler.push(`geändert: ${datei}`);
  }
}

if (fehler.length) {
  console.error('Dateien aus dem Home Repo wurden hier geändert. Sie gehören nach pauldiepold/home, siehe .claude/rules/home.md:');
  fehler.forEach((zeile) => console.error(`  ${zeile}`));
  process.exit(1);
}

console.log(`home-sync: ${Object.keys(dateien).length} Dateien unverändert`);

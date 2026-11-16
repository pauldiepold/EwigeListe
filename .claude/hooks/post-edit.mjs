// PostToolUse-Hook für Edit/Write: formatiert Frontend-Dateien mit Prettier, prüft PHP-Syntax und formatiert PHP mit Pint.
// Welche Dateien Prettier bekommt, steht in `.claude/projekt.json` (`prettier.muster`, `prettier.ohne`).
// Exit-Code 2 meldet einen PHP-Syntaxfehler an Claude zurück.
import { spawnSync } from 'node:child_process';
import { readFileSync } from 'node:fs';
import path from 'node:path';

const input = JSON.parse(await new Promise((resolve) => {
  let data = '';
  process.stdin.on('data', (chunk) => (data += chunk));
  process.stdin.on('end', () => resolve(data || '{}'));
}));

const filePath = input.tool_input?.file_path ?? input.tool_response?.filePath;
if (!filePath) process.exit(0);

// In Subagent-Worktrees (.claude/worktrees/<name>/) gelten deren Pfade und Binaries.
const mainDir = process.env.CLAUDE_PROJECT_DIR ?? process.cwd();
const fromMain = path.relative(mainDir, filePath).split(path.sep).join('/');
const worktree = fromMain.match(/^\.claude\/worktrees\/[^/]+\//)?.[0] ?? '';
const projectDir = path.join(mainDir, worktree);
const relative = fromMain.slice(worktree.length);
const isWindows = process.platform === 'win32';

let projekt = {};
try {
  projekt = JSON.parse(readFileSync(path.join(projectDir, '.claude', 'projekt.json'), 'utf8'));
} catch {
  // Ohne Konfiguration läuft nur der PHP-Teil.
}

const muster = projekt.prettier?.muster ? new RegExp(projekt.prettier.muster) : null;
const ohne = projekt.prettier?.ohne ? new RegExp(projekt.prettier.ohne) : null;

if (muster?.test(relative) && !ohne?.test(relative)) {
  const prettier = path.join(projectDir, 'node_modules', '.bin', isWindows ? 'prettier.cmd' : 'prettier');
  spawnSync(`"${prettier}" --write "${filePath}"`, { cwd: projectDir, stdio: 'ignore', shell: true });
}

if (relative.endsWith('.php') && !relative.startsWith('vendor/')) {
  const result = spawnSync(`php -l "${filePath}"`, { cwd: projectDir, encoding: 'utf8', shell: true });
  if (result.status !== 0) {
    process.stderr.write(`PHP-Syntaxfehler in ${relative}:\n${result.stdout}${result.stderr}`);
    process.exit(2);
  }

  if (!relative.endsWith('.blade.php')) {
    const pint = path.join(projectDir, 'vendor', 'bin', isWindows ? 'pint.bat' : 'pint');
    spawnSync(`"${pint}" --quiet "${filePath}"`, { cwd: projectDir, stdio: 'ignore', shell: true });
  }
}

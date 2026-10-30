// PostToolUse-Hook für Edit/Write: formatiert Inertia-Dateien mit Prettier, prüft PHP-Syntax und formatiert PHP mit Pint.
// Exit-Code 2 meldet einen PHP-Syntaxfehler an Claude zurück.
import { spawnSync } from 'node:child_process';
import path from 'node:path';

const input = JSON.parse(await new Promise((resolve) => {
  let data = '';
  process.stdin.on('data', (chunk) => (data += chunk));
  process.stdin.on('end', () => resolve(data || '{}'));
}));

const filePath = input.tool_input?.file_path ?? input.tool_response?.filePath;
if (!filePath) process.exit(0);

const projectDir = process.env.CLAUDE_PROJECT_DIR ?? process.cwd();
const relative = path.relative(projectDir, filePath).split(path.sep).join('/');
const isWindows = process.platform === 'win32';

const generated = /^resources\/js\/inertia\/(actions|routes|wayfinder)\//;

if (/^resources\/js\/inertia\/.+\.(ts|vue)$/.test(relative) && !generated.test(relative)) {
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

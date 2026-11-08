<script setup lang="ts">
import { nextTick, onUnmounted, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { RoundGame, RoundPlayer } from '@/types/round-show';

const HIGHLIGHT_MILLISECONDS = 2000;

/** Ab 5 Spielern setzt immer jemand aus, bei genau 5 der Geber. */
const DIM_SITTING_OUT_FROM_PLAYERS = 5;

const HEAD_CELL_CLASSES =
  'sticky top-0 z-10 bg-default align-bottom shadow-[inset_0_-2px_0_var(--ui-color-neutral-400)] dark:shadow-[inset_0_-2px_0_var(--ui-color-neutral-600)]';

/** Oben Platz für den stehenden Kopf, unten (nur Handy und Tablet) für den Plus-Button. */
const ROW_SCROLL_MARGIN_CLASSES = 'scroll-mt-24 scroll-mb-24 lg:scroll-mb-4';

const props = defineProps<{
  players: RoundPlayer[];
  games: RoundGame[];
  onlinePlayerIds: number[];
  /** Eine gerade gespeicherte Änderung, deren Antwort noch aussteht: Die Zeile zeigt ein Skeleton. */
  pendingGame?: 'new' | 'edited' | null;
  /** Zum Löschen vorgemerkt (Rückfrage offen oder Löschen läuft): Die Zeile ist rot getönt. */
  markedGameId?: number | null;
  /** Ausstehendes Löschen: Die Zeile zeigt bis zur Antwort ein Skeleton, danach ist sie weg. */
  deletingGameId?: number | null;
}>();

function sitsOut(player: RoundPlayer): boolean {
  return props.players.length >= DIM_SITTING_OUT_FROM_PLAYERS && !player.isActive;
}

const tableWrapper = ref<HTMLElement | null>(null);

/**
 * Holt eine neue oder geänderte Zeile ins Bild, auch wenn sie von jemand anderem kommt. Schon
 * sichtbare Zeilen bleiben stehen (`nearest`). Die Scroll-Abstände der Zeilen halten sie frei
 * vom stehenden Tabellenkopf und vom schwebenden Plus-Button.
 */
async function scrollToRow(selector: string): Promise<void> {
  await nextTick();
  tableWrapper.value
    ?.querySelector(selector)
    ?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Schon beim Absenden: Das Skeleton des neuen Spiels zeigt, wo es gleich erscheint.
watch(
  () => props.pendingGame,
  (pendingGame) => {
    if (pendingGame === 'new') {
      scrollToRow('[data-pending-game]');
    }
  },
);

const highlightedGames = ref<Record<number, 'new' | 'edited'>>({});
const highlightTimers = new Set<ReturnType<typeof setTimeout>>();

/**
 * Beim ersten Laden leuchtet nichts, danach jede neu erschienene Zeile grün und jede
 * inhaltlich geänderte (bearbeitete) Zeile gelb.
 */
watch(
  () => props.games.map((game) => [game.id, JSON.stringify(game)] as const),
  (games, previousGames) => {
    const previousContents = new Map(previousGames);
    const changes: Record<number, 'new' | 'edited'> = {};
    for (const [gameId, content] of games) {
      const previousContent = previousContents.get(gameId);
      if (previousContent === undefined) {
        changes[gameId] = 'new';
      } else if (previousContent !== content) {
        changes[gameId] = 'edited';
      }
    }

    const changedGameIds = Object.keys(changes).map(Number);
    if (changedGameIds.length === 0) {
      return;
    }

    highlightedGames.value = { ...highlightedGames.value, ...changes };
    scrollToRow(`[data-game-id="${changedGameIds.at(-1)}"]`);
    const timer = setTimeout(() => {
      highlightTimers.delete(timer);
      highlightedGames.value = Object.fromEntries(
        Object.entries(highlightedGames.value).filter(
          ([gameId]) => !changedGameIds.includes(Number(gameId)),
        ),
      );
    }, HIGHLIGHT_MILLISECONDS);
    highlightTimers.add(timer);
  },
);

onUnmounted(() => {
  highlightTimers.forEach(clearTimeout);
});

function isPendingRow(game: RoundGame): boolean {
  return (
    game.id === props.deletingGameId ||
    (props.pendingGame === 'edited' && game.id === props.games.at(-1)?.id)
  );
}

/**
 * Hervorhebungen blenden schnell ein und langsam aus: Die Dauer gilt beim Wechsel in den
 * jeweiligen Zustand. Neue Zeilen starten per `starting:` transparent, sonst stünde ihr Grün
 * sofort da, weil es keinen vorherigen Zustand gibt, von dem aus überblendet würde.
 */
function rowBackground(game: RoundGame): string {
  if (game.id === props.markedGameId) {
    return 'bg-error/40 duration-300';
  }
  const highlight = highlightedGames.value[game.id];
  if (highlight === 'new') {
    return 'bg-success/30 duration-300 starting:bg-transparent';
  }
  if (highlight === 'edited') {
    return 'bg-warning/30 duration-300';
  }
  if (game.isMisplay) {
    return 'bg-error/15 duration-1000';
  }
  if (game.isSolo) {
    return 'bg-elevated duration-1000';
  }
  return 'duration-1000';
}
</script>

<template>
  <!--
    Am Handy bis an den Rand: hebt das Seiten-Padding auf. `overflow-x-clip` statt `-auto`, denn ein
    Scroll-Container hielte den stehenden Kopf nicht am oberen Rand.
  -->
  <div
    ref="tableWrapper"
    class="-mx-4 overflow-x-clip sm:mx-0 [&_:is(td,th):first-child]:pl-2 [&_:is(td,th):last-child]:pr-2"
  >
    <table class="w-full table-fixed text-center tabular-nums">
      <thead>
        <tr>
          <th
            v-for="player in players"
            :key="player.id"
            class="px-0.5 pt-1.5 pb-2 font-normal"
            :class="HEAD_CELL_CLASSES"
          >
            <component
              :is="player.path ? Link : 'span'"
              :href="player.path ?? undefined"
              class="flex flex-col items-center gap-2 rounded-md px-0.5 pt-1.5 pb-1 no-underline"
            >
              <UAvatar
                :src="player.avatarPath ?? undefined"
                :alt="player.firstName"
                :class="{
                  'opacity-40': sitsOut(player),
                  'shadow-[0_0_10px_2px] shadow-success/60': onlinePlayerIds.includes(player.id),
                }"
              />
              <span
                class="block w-full truncate text-sm leading-4 font-semibold"
                :class="[
                  player.isDealer ? 'text-primary' : 'text-highlighted',
                  { 'opacity-40': sitsOut(player) },
                ]"
              >
                {{ player.firstName }}
              </span>
            </component>
          </th>
          <th
            class="px-0.5 pt-2 pb-3 text-sm leading-4 font-semibold text-highlighted"
            :class="HEAD_CELL_CLASSES"
          >
            Punkte
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="game in games"
          :key="game.id"
          :data-game-id="game.id"
          class="transition-colors"
          :class="[
            ROW_SCROLL_MARGIN_CLASSES,
            rowBackground(game),
            game.endsDealerCycle
              ? 'border-b-2 border-(--ui-color-neutral-400) dark:border-(--ui-color-neutral-600)'
              : 'border-b border-accented/70',
          ]"
        >
          <td
            v-for="(score, seat) in game.scores"
            :key="seat"
            class="px-1 py-1.5"
            :class="{ 'font-bold': score.won }"
          >
            <USkeleton v-if="isPendingRow(game)" class="mx-auto h-5 w-8" />
            <template v-else>{{ score.points ?? '-' }}</template>
          </td>
          <td class="px-1 py-1.5">
            <USkeleton v-if="isPendingRow(game)" class="mx-auto h-5 w-6" />
            <template v-else>{{ game.points }}</template>
          </td>
        </tr>
        <tr
          v-if="pendingGame === 'new'"
          data-pending-game
          class="border-b border-accented/70"
          :class="ROW_SCROLL_MARGIN_CLASSES"
        >
          <td v-for="player in players" :key="player.id" class="px-1 py-1.5">
            <USkeleton class="mx-auto h-5 w-8" />
          </td>
          <td class="px-1 py-1.5"><USkeleton class="mx-auto h-5 w-6" /></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

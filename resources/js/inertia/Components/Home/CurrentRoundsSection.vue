<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { show as showRound } from '@/routes/rounds';
import type { HomeRound } from '@/types/home';

const props = defineProps<{
  rounds: HomeRound[];
}>();

/** Bis so lange nach dem letzten Spiel gilt eine Runde als „läuft“. */
const RUNNING_THRESHOLD_MINUTES = 30;

const relativeTime = new Intl.RelativeTimeFormat('de', { numeric: 'auto', style: 'short' });

// Tickt jede halbe Minute, damit „vor 5 Min.“ und „läuft“ ohne Neuladen aktuell bleiben.
const now = ref(Date.now());
let timer: ReturnType<typeof setInterval> | undefined;
onMounted(() => {
  timer = setInterval(() => (now.value = Date.now()), 30_000);
});
onUnmounted(() => clearInterval(timer));

function minutesSince(isoDate: string): number {
  return Math.max(0, Math.floor((now.value - Date.parse(isoDate)) / 60_000));
}

function isRunning(round: HomeRound): boolean {
  return round.lastGameAt !== null && minutesSince(round.lastGameAt) < RUNNING_THRESHOLD_MINUTES;
}

function lastGameLabel(isoDate: string): string {
  const minutes = minutesSince(isoDate);
  if (minutes < 1) {
    return 'gerade eben';
  }
  if (minutes < 60) {
    return relativeTime.format(-minutes, 'minute');
  }
  return relativeTime.format(-Math.floor(minutes / 60), 'hour');
}

function playerNames(round: HomeRound): string {
  return round.players.map((player) => player.firstName).join(', ');
}

/** Alle mit dem höchsten Punktestand; bei Gleichstand mehrere. Ohne Plus-Punkte gibt es niemanden vorn. */
function leaderOf(round: HomeRound): { names: string; points: number } | null {
  const points = Math.max(...round.players.map((player) => player.points));
  if (!Number.isFinite(points) || points <= 0) {
    return null;
  }
  const names = round.players
    .filter((player) => player.points === points)
    .map((player) => player.firstName)
    .join(' & ');
  return { names, points };
}

const cards = computed(() => props.rounds.map((round) => ({ round, leader: leaderOf(round) })));
</script>

<template>
  <section v-if="rounds.length > 0" class="mb-8">
    <h2 class="mb-4 text-xl font-semibold text-highlighted">Aktuelle Runden</h2>

    <div class="grid gap-3 sm:grid-cols-2">
      <Link
        v-for="{ round, leader } in cards"
        :key="round.id"
        :href="showRound.url({ round: round.id })"
        class="group flex items-center gap-3 rounded-lg border p-3 shadow-sm transition hover:shadow-md focus-visible:outline-2 focus-visible:outline-primary"
        :class="
          round.isOwn
            ? 'border-primary/40 bg-primary/10 hover:border-primary/70'
            : 'border-default bg-elevated hover:border-accented'
        "
      >
        <div class="min-w-0 flex-1 space-y-2.5">
          <div class="flex items-center gap-1.5 text-sm text-muted">
            <template v-if="isRunning(round)">
              <span class="relative flex size-2" aria-hidden="true">
                <span
                  class="absolute inline-flex size-full animate-ping rounded-full bg-success opacity-75 motion-reduce:animate-none"
                />
                <span class="relative inline-flex size-2 rounded-full bg-success" />
              </span>
              <span class="font-medium text-success">läuft</span>
              <span aria-hidden="true">·</span>
            </template>
            <time v-if="round.lastGameAt" :datetime="round.lastGameAt">
              {{ lastGameLabel(round.lastGameAt) }}
            </time>
            <span aria-hidden="true">·</span>
            <span>{{ round.gamesCount }} {{ round.gamesCount === 1 ? 'Spiel' : 'Spiele' }}</span>
            <UIcon
              v-if="round.isLive"
              name="i-lucide-wifi"
              class="ms-auto size-4 shrink-0 text-primary"
              aria-label="Online"
            />
          </div>

          <div class="flex items-center gap-3">
            <UAvatarGroup size="sm" :max="4" class="shrink-0">
              <UAvatar
                v-for="player in round.players"
                :key="player.id"
                :src="player.avatarPath ?? undefined"
                :alt="player.firstName"
              />
            </UAvatarGroup>
            <p class="min-w-0 font-semibold text-pretty text-highlighted">
              {{ playerNames(round) }}
            </p>
          </div>

          <p v-if="leader" class="flex items-center gap-1.5 text-sm">
            <UIcon name="i-lucide-crown" class="size-4 shrink-0 text-warning" aria-label="Vorn" />
            <span class="min-w-0 truncate font-medium text-highlighted">{{ leader.names }}</span>
            <span class="shrink-0 text-muted tabular-nums">{{ leader.points }} Punkte</span>
          </p>
        </div>

        <UIcon
          name="i-lucide-chevron-right"
          class="size-5 shrink-0 text-dimmed transition group-hover:translate-x-0.5 group-hover:text-highlighted"
          aria-hidden="true"
        />
      </Link>
    </div>
  </section>
</template>

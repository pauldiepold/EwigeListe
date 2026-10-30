<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CurrentRoundsSection from '@/Components/Home/CurrentRoundsSection.vue';
import HomeDataTable from '@/Components/Home/HomeDataTable.vue';
import HomeGamesChart from '@/Components/Home/HomeGamesChart.vue';
import AppLoadingIndicator from '@/Components/Ui/AppLoadingIndicator.vue';
import type { HomeGamesChartPayload, HomeGroup, HomeRound } from '@/types/home';

defineProps<{
  group: HomeGroup;
  currentRounds: HomeRound[];
  homeGamesChart?: HomeGamesChartPayload;
}>();
</script>

<template>
  <Head title="Startseite" />

  <AppLayout title="Startseite">
    <CurrentRoundsSection :rounds="currentRounds" />

    <section class="mb-8">
      <h2 class="mb-4 text-xl font-semibold text-highlighted">Rekorde</h2>
      <HomeDataTable :rows="group.records" />
    </section>

    <section class="mb-8">
      <h2 class="mb-4 text-xl font-semibold text-highlighted">Statistiken</h2>
      <HomeDataTable :rows="group.stats" />
    </section>

    <section>
      <h2 class="mb-4 text-xl font-semibold text-highlighted">Anzahl der Spiele</h2>

      <Deferred data="homeGamesChart">
        <template #fallback>
          <div
            class="mx-auto max-w-3xl rounded border border-default bg-elevated p-4"
            aria-busy="true"
          >
            <div
              class="flex min-h-[360px] flex-col items-center justify-center gap-2 rounded bg-muted/10"
            >
              <AppLoadingIndicator label="Chart wird geladen…" size="md" />
            </div>
          </div>
        </template>

        <HomeGamesChart v-if="homeGamesChart" :chart-data="homeGamesChart" />
      </Deferred>
    </section>
  </AppLayout>
</template>

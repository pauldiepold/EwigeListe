<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeferredGamesChart from '@/Components/Groups/DeferredGamesChart.vue';
import GroupStatisticsTable from '@/Components/Groups/GroupStatisticsTable.vue';
import CurrentRoundsSection from '@/Components/Home/CurrentRoundsSection.vue';
import type { GamesChartPayload, GroupStatistics } from '@/types/groups-show';
import type { HomeRound } from '@/types/home';

defineProps<{
  group: GroupStatistics;
  currentRounds: HomeRound[];
  homeGamesChart?: GamesChartPayload;
}>();
</script>

<template>
  <Head title="Startseite" />

  <AppLayout title="Startseite">
    <CurrentRoundsSection :rounds="currentRounds" />

    <section class="mb-8">
      <h2 class="mb-4 text-xl font-semibold text-highlighted">Rekorde</h2>
      <GroupStatisticsTable :rows="group.records" />
    </section>

    <section class="mb-8">
      <h2 class="mb-4 text-xl font-semibold text-highlighted">Statistiken</h2>
      <GroupStatisticsTable :rows="group.stats" />
    </section>

    <section>
      <h2 class="mb-4 text-xl font-semibold text-highlighted">Anzahl der Spiele</h2>
      <DeferredGamesChart prop="homeGamesChart" :chart-data="homeGamesChart" />
    </section>
  </AppLayout>
</template>

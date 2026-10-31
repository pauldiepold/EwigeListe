<script setup lang="ts">
import { Deferred, Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GamesChart from '@/Components/Groups/GamesChart.vue';
import GamesChartLoading from '@/Components/Groups/GamesChartLoading.vue';
import GroupStatisticsTable from '@/Components/Groups/GroupStatisticsTable.vue';
import CurrentRoundsSection from '@/Components/Home/CurrentRoundsSection.vue';
import type { HomeProps } from '@/types/home';

defineProps<HomeProps>();
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

      <Deferred data="homeGamesChart">
        <template #fallback>
          <GamesChartLoading />
        </template>

        <GamesChart v-if="homeGamesChart" :chart-data="homeGamesChart" />
      </Deferred>
    </section>
  </AppLayout>
</template>

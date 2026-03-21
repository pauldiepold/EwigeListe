<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import CurrentRoundsSection from '@/Components/Home/CurrentRoundsSection.vue';
import HomeDataTable from '@/Components/Home/HomeDataTable.vue';
import HomeGamesChart from '@/Components/Home/HomeGamesChart.vue';
import type { HomeGroup, HomeRound } from '@/types/home';

defineProps<{
  group: HomeGroup;
  currentRounds: HomeRound[];
}>();
</script>

<template>
  <Head title="Startseite" />

  <AppLayout title="Startseite">
    <CurrentRoundsSection :rounds="currentRounds" />

    <section class="mb-8">
      <h2 class="mb-4 text-xl font-semibold">Rekorde</h2>
      <HomeDataTable :rows="group.records" />
    </section>

    <section class="mb-8">
      <h2 class="mb-4 text-xl font-semibold">Statistiken</h2>
      <HomeDataTable :rows="group.stats" />
    </section>

    <section>
      <h2 class="mb-4 text-xl font-semibold">Anzahl der Spiele</h2>
      <HomeGamesChart :group-id="group.id" />
    </section>
  </AppLayout>
</template>

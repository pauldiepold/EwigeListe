<script setup lang="ts">
import GroupStatisticsTable from '@/Components/Groups/GroupStatisticsTable.vue';
import type { ProfileStatistics } from '@/types/players-show';

defineProps<{
  profile: ProfileStatistics;
  badgeCount: number;
}>();
</script>

<template>
  <section class="mx-auto max-w-md space-y-6">
    <template v-if="profile.statistics">
      <p v-if="badgeCount > 0" class="flex items-center gap-2 text-sm">
        <span class="text-muted">Abzeichen:</span>
        <span class="font-semibold text-highlighted">{{ badgeCount }}</span>
        <UIcon name="i-lucide-crown" class="size-5 text-yellow-500" aria-hidden="true" />
      </p>

      <GroupStatisticsTable v-for="(rows, index) in profile.statistics" :key="index" :rows="rows" />

      <div v-if="profile.opportunityCosts">
        <h2 class="mb-2 font-semibold text-highlighted">Opportunitätskosten:</h2>
        <GroupStatisticsTable :rows="profile.opportunityCosts" />
      </div>
    </template>
    <p v-else class="text-lg text-toned">Statistiken werden ab dem 10. Spiel angezeigt.</p>

    <p class="text-muted">{{ profile.joinedLabel }}</p>
  </section>
</template>

<script setup lang="ts">
import BadgeCard from '@/Components/Groups/BadgeCard.vue';
import type { GroupBadgesOfType } from '@/types/groups-show';

defineProps<{
  badges: GroupBadgesOfType[];
}>();
</script>

<template>
  <div class="mx-auto max-w-2xl gap-4 sm:flex">
    <div v-for="badgesOfType in badges" :key="badgesOfType.type" class="mb-10 sm:flex-1">
      <section v-for="{ year, badges: badgesOfYear } in badgesOfType.years" :key="year">
        <h3 class="my-4 text-center font-semibold text-highlighted">
          Die meisten {{ badgesOfType.type === 'points' ? 'Punkte' : 'Spiele' }} {{ year }}:
        </h3>
        <div class="space-y-4">
          <BadgeCard
            v-for="badge in badgesOfYear"
            :key="badge.date"
            :badge="badge"
            :type="badgesOfType.type"
          />
        </div>
      </section>
    </div>
  </div>
</template>

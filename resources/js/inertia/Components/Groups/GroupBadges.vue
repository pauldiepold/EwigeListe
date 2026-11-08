<script setup lang="ts">
import { computed } from 'vue';
import { BADGE_TYPES, monthName } from '@/Components/Groups/badges';
import ProfileLink from '@/Components/Ui/ProfileLink.vue';
import type { BadgeYear } from '@/types/groups-show';

const props = defineProps<{
  badges: BadgeYear[];
}>();

const years = computed(() =>
  props.badges.map(({ year, months }) => ({
    year,
    rows: months.map((month) => ({
      month: month.month,
      winners: BADGE_TYPES.map((type) => ({ type, winner: month[type.key] })),
    })),
  })),
);
</script>

<template>
  <div class="mx-auto max-w-2xl space-y-6">
    <section v-for="{ year, rows } in years" :key="year">
      <h3 class="mb-2 text-lg font-bold text-highlighted">{{ year }}</h3>
      <div class="overflow-x-auto rounded-lg border border-default">
        <table class="w-full border-collapse text-left text-sm">
          <thead class="bg-elevated text-xs text-muted">
            <tr>
              <th scope="col" class="px-3 py-2 font-semibold">Monat</th>
              <th
                v-for="type in BADGE_TYPES"
                :key="type.key"
                scope="col"
                class="px-3 py-2 font-semibold"
              >
                <span class="inline-flex items-center gap-1.5">
                  <UIcon
                    :name="type.icon"
                    class="size-4 shrink-0"
                    :class="type.iconClass"
                    aria-hidden="true"
                  />
                  {{ type.label }}
                </span>
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="row in rows" :key="row.month" class="border-t border-default">
              <th scope="row" class="px-3 py-2 font-medium text-toned">
                {{ monthName(row.month) }}
              </th>
              <td v-for="{ type, winner } in row.winners" :key="type.key" class="px-3 py-2">
                <template v-if="winner">
                  <ProfileLink :href="winner.path">{{ winner.firstName }}</ProfileLink>
                  <span class="ml-1.5 text-muted tabular-nums">{{ winner.value }}</span>
                </template>
                <template v-else>
                  <span class="text-dimmed" aria-hidden="true">–</span>
                  <span class="sr-only">niemand</span>
                </template>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>

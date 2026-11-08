<script setup lang="ts">
import { computed } from 'vue';
import { BADGE_TYPES, monthName } from '@/Components/Groups/badges';
import type { BadgeYear } from '@/types/groups-show';

const props = defineProps<{
  years: BadgeYear[];
}>();

const MONTHS = Array.from({ length: 12 }, (_, index) => index + 1);

/** Pro Jahr alle zwölf Monate, damit auch die Lücken sichtbar sind. */
const calendars = computed(() =>
  props.years.map(({ year, months }) => {
    const monthsByNumber = new Map(months.map((month) => [month.month, month]));
    const rows = BADGE_TYPES.map((type) => {
      const cells = MONTHS.map((month) => ({
        month,
        value: monthsByNumber.get(month)?.[type.key]?.value ?? null,
      }));
      return { type, cells, count: cells.filter((cell) => cell.value !== null).length };
    });

    return {
      year,
      rows,
      summary: rows.map((row) => `${row.count}× ${row.type.shortLabel}`).join(' · '),
    };
  }),
);
</script>

<template>
  <div class="mx-auto max-w-2xl space-y-4">
    <section
      v-for="calendar in calendars"
      :key="calendar.year"
      class="rounded-lg border border-default p-3 sm:p-5"
    >
      <div class="mb-3 flex items-baseline justify-between gap-3">
        <h3 class="text-lg font-bold text-highlighted">{{ calendar.year }}</h3>
        <p class="text-sm text-muted">{{ calendar.summary }}</p>
      </div>

      <div
        class="grid grid-cols-[1rem_repeat(12,minmax(0,1fr))] items-center gap-0.5 sm:grid-cols-[4.5rem_repeat(12,minmax(0,1fr))] sm:gap-1"
      >
        <span aria-hidden="true" />
        <span
          v-for="month in MONTHS"
          :key="month"
          class="text-center text-[11px] font-semibold text-muted"
          aria-hidden="true"
        >
          {{ monthName(month).charAt(0) }}
        </span>

        <template v-for="row in calendar.rows" :key="row.type.key">
          <span class="flex items-center gap-1.5 text-sm text-toned">
            <UIcon
              :name="row.type.icon"
              class="size-4 shrink-0"
              :class="row.type.iconClass"
              aria-hidden="true"
            />
            <span class="sr-only sm:not-sr-only">{{ row.type.shortLabel }}</span>
          </span>
          <span
            v-for="cell in row.cells"
            :key="cell.month"
            class="flex h-8 items-center justify-center rounded-md text-[11px] font-bold tabular-nums sm:h-9 sm:text-xs"
            :class="cell.value === null ? 'bg-elevated' : row.type.cellClass"
            :title="
              cell.value === null
                ? undefined
                : `${row.type.label} im ${monthName(cell.month)} ${calendar.year}: ${cell.value}`
            "
          >
            <template v-if="cell.value !== null">
              <span class="sr-only">{{ row.type.label }} im {{ monthName(cell.month) }}: </span
              >{{ cell.value }}
            </template>
          </span>
        </template>
      </div>
    </section>
  </div>
</template>

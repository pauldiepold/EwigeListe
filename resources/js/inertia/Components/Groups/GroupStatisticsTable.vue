<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { RecordRow, StatRow } from '@/types/groups-show';

defineProps<{
  rows: Array<RecordRow | StatRow>;
}>();

function holderSeparator(index: number, count: number): string {
  if (index === count - 1) {
    return '';
  }
  return index === count - 2 ? ' und ' : ', ';
}
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full max-w-3xl border-collapse text-left text-sm">
      <tbody>
        <tr v-for="row in rows" :key="row.label" class="border-b border-default last:border-b-0">
          <td class="py-2 pr-4 text-muted">{{ row.label }}</td>
          <td class="w-24 py-2 pr-4 font-semibold text-highlighted">{{ row.value }}</td>
          <td v-if="'holders' in row" class="py-2 text-muted">
            <template v-for="(holder, index) in row.holders" :key="holder.path">
              <Link :href="holder.path" class="text-primary hover:underline">{{ holder.name }}</Link
              >{{ holderSeparator(index, row.holders.length) }}
            </template>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

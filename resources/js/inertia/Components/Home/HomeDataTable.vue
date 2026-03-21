<script setup lang="ts">
import type { RecordRow, StatRow } from '@/types/home';

defineProps<{
  rows: Array<RecordRow | StatRow>;
}>();
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full max-w-3xl border-collapse text-left text-sm">
      <tbody>
        <tr v-for="(row, index) in rows" :key="index" class="border-b border-default last:border-b-0">
          <td class="py-2 pr-4 text-muted" v-html="row.labelHtml" />
          <td class="w-24 py-2 pr-4 font-semibold text-highlighted">{{ row.value }}</td>
          <td v-if="'detailHtml' in row" class="py-2 text-muted" v-html="row.detailHtml" />
        </tr>
      </tbody>
    </table>
  </div>
</template>

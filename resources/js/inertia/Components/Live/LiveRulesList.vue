<script setup lang="ts">
import { computed } from 'vue';
import { visibleRules } from '@/Components/Live/liveRules';
import type { LiveRules } from '@/types/live';

const props = defineProps<{
  rules: LiveRules;
}>();

const rules = computed(() => visibleRules(props.rules));
</script>

<template>
  <ul class="divide-y divide-default">
    <li v-for="rule in rules" :key="rule.key" class="flex items-start justify-between gap-4 py-2">
      <div>
        <p class="font-medium text-highlighted">{{ rule.label }}</p>
        <p class="text-sm text-muted">{{ rule.description }}</p>
      </div>
      <UBadge
        :label="props.rules[rule.key] ? 'an' : 'aus'"
        :color="props.rules[rule.key] ? 'primary' : 'neutral'"
        variant="subtle"
        class="shrink-0"
      />
    </li>
  </ul>
</template>

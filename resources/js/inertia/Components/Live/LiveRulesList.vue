<script setup lang="ts">
import { computed } from 'vue';
import { visibleLiveRules, type LiveRules } from '@/Components/Live/liveRules';

const props = defineProps<{
  rules: LiveRules;
}>();

const definitions = computed(() => visibleLiveRules(props.rules));
</script>

<template>
  <ul class="divide-y divide-default">
    <li
      v-for="rule in definitions"
      :key="rule.key"
      class="flex items-start justify-between gap-4 py-2"
    >
      <div>
        <p class="font-medium text-highlighted">{{ rule.label }}</p>
        <p class="text-sm text-muted">{{ rule.description }}</p>
      </div>
      <UBadge
        :label="rules[rule.key] ? 'an' : 'aus'"
        :color="rules[rule.key] ? 'primary' : 'neutral'"
        variant="subtle"
        class="shrink-0"
      />
    </li>
  </ul>
</template>

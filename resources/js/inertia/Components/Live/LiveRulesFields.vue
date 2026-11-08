<script setup lang="ts">
import { computed } from 'vue';
import {
  liveRuleDefinitions,
  type LiveRuleDefinition,
  type LiveRuleKey,
  type LiveRules,
} from '@/Components/Live/liveRules';

/** Die Schalter für die Regeln einer Live-Runde. Abhängige Schalter erscheinen nur bei aktiver übergeordneter Regel. */
const rules = defineModel<LiveRules>({ required: true });

const visibleDefinitions = computed(() =>
  liveRuleDefinitions.filter(
    (definition) => definition.parent === undefined || rules.value[definition.parent],
  ),
);

function setRule(key: LiveRuleKey, value: boolean): void {
  rules.value = { ...rules.value, [key]: value };
}

function isDependent(definition: LiveRuleDefinition): boolean {
  return definition.parent !== undefined;
}
</script>

<template>
  <div class="divide-y divide-default rounded-lg border border-default bg-default">
    <div
      v-for="definition in visibleDefinitions"
      :key="definition.key"
      class="px-4 py-3"
      :class="{ 'ml-4 border-l-2 border-l-primary/40 py-2': isDependent(definition) }"
    >
      <USwitch
        :model-value="rules[definition.key]"
        :label="definition.label"
        :description="definition.description"
        color="primary"
        class="w-full"
        :ui="{ root: 'justify-between flex-row-reverse gap-4', wrapper: 'flex-1' }"
        @update:model-value="setRule(definition.key, $event)"
      />
    </div>
  </div>
</template>

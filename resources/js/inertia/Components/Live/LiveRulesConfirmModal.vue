<script setup lang="ts">
import { computed } from 'vue';
import { liveRuleOverview, type LiveRules } from '@/Components/Live/liveRules';

/**
 * Rückfrage vor dem Anlegen einer Online-Runde. Die Regeln lassen sich danach nicht mehr ändern,
 * und eingespielte Benutzer übersehen sie sonst im Formular.
 */
const props = defineProps<{
  rules: LiveRules;
  loading?: boolean;
}>();

const open = defineModel<boolean>('open', { default: false });

defineEmits<{
  confirm: [];
}>();

const overview = computed(() => liveRuleOverview(props.rules));
</script>

<template>
  <UModal
    v-model:open="open"
    title="Mit diesen Regeln spielen?"
    description="Nach dem Erstellen lassen sich die Regeln nicht mehr ändern."
    :close="false"
    :ui="{ content: 'top-4 translate-y-0 sm:top-16' }"
  >
    <template #body>
      <ul class="space-y-2">
        <li
          v-for="rule in overview"
          :key="rule.label"
          :aria-label="`${rule.label}: ${rule.active ? 'an' : 'aus'}`"
          class="flex items-center gap-2"
          :class="{ 'text-muted': !rule.active }"
        >
          <UIcon
            :name="rule.active ? 'i-lucide-check' : 'i-lucide-x'"
            class="size-4 shrink-0"
            :class="rule.active ? 'text-primary' : 'text-muted'"
            aria-hidden="true"
          />
          <span>{{ rule.label }}</span>
        </li>
      </ul>
    </template>

    <template #footer>
      <div class="flex w-full justify-end gap-3">
        <UButton
          color="neutral"
          variant="outline"
          label="Regeln ändern"
          :disabled="loading"
          @click="open = false"
        />
        <UButton
          color="primary"
          icon="i-lucide-play"
          label="Runde erstellen"
          :loading="loading"
          @click="$emit('confirm')"
        />
      </div>
    </template>
  </UModal>
</template>

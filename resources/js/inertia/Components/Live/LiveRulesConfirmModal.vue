<script setup lang="ts">
import { computed } from 'vue';
import { activeLiveRuleLabels, type LiveRules } from '@/Components/Live/liveRules';

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

const labels = computed(() => activeLiveRuleLabels(props.rules));
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
      <ul v-if="labels.length" class="space-y-2">
        <li v-for="label in labels" :key="label" class="flex items-center gap-2">
          <UIcon name="i-lucide-check" class="size-4 shrink-0 text-primary" aria-hidden="true" />
          <span>{{ label }}</span>
        </li>
      </ul>
      <p v-else class="text-muted">Keine Sonderregeln.</p>
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

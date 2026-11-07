<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
  label: string;
  confirmLabel: string;
  loading?: boolean;
  icon?: string;
}>();

const emit = defineEmits<{
  confirm: [];
}>();

const confirming = ref(false);

function confirm(): void {
  confirming.value = false;
  emit('confirm');
}
</script>

<template>
  <UButton
    v-if="!confirming"
    color="neutral"
    variant="outline"
    :label="label"
    :icon="icon"
    :loading="loading"
    @click="confirming = true"
  />
  <div v-else class="flex flex-wrap justify-center gap-2">
    <UButton color="neutral" variant="outline" label="Abbrechen" @click="confirming = false" />
    <UButton color="error" :label="confirmLabel" @click="confirm" />
  </div>
</template>

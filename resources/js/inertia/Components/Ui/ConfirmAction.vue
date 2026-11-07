<script setup lang="ts">
import { ref } from 'vue';

defineProps<{
  label: string;
  confirmLabel: string;
  loading?: boolean;
  icon?: string;
  size?: 'sm' | 'md';
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
    :size="size"
    :label="label"
    :icon="icon"
    :loading="loading"
    @click="confirming = true"
  />
  <div v-else class="flex flex-wrap justify-center gap-2">
    <UButton
      color="neutral"
      variant="outline"
      :size="size"
      label="Abbrechen"
      @click="confirming = false"
    />
    <UButton color="error" :size="size" :label="confirmLabel" @click="confirm" />
  </div>
</template>

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

<!--
  Ohne Wrapper: Die Buttons der Rückfrage reihen sich direkt in den umgebenden Container ein,
  z. B. in eine `UFieldGroup`, deren Rundungen an `:first-child`/`:last-child` hängen.
  Eigene Abstände braucht dieser deshalb selbst (`gap-*`).
-->
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
  <template v-else>
    <UButton color="neutral" variant="outline" label="Abbrechen" @click="confirming = false" />
    <UButton color="error" :label="confirmLabel" @click="confirm" />
  </template>
</template>

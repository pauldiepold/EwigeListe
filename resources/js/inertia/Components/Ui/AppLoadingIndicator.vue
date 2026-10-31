<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
  defineProps<{
    /** Kurzer Hinweistext unter oder neben dem Spinner */
    label?: string;
    size?: 'sm' | 'md' | 'lg';
    /** Label rechts vom Spinner statt darunter */
    inline?: boolean;
  }>(),
  {
    label: '',
    size: 'md',
    inline: false,
  },
);

const spinnerClass = computed(() => {
  const base =
    'shrink-0 rounded-full border-2 border-muted border-t-orange-500 animate-spin motion-reduce:animate-none';

  if (props.size === 'sm') {
    return `${base} size-6 border-[2px]`;
  }

  if (props.size === 'lg') {
    return `${base} size-14 border-[3px]`;
  }

  return `${base} size-10 border-[2px]`;
});
</script>

<template>
  <div
    class="flex gap-3 text-muted"
    :class="inline ? 'flex-row items-center' : 'flex-col items-center justify-center'"
    role="status"
    aria-live="polite"
  >
    <span class="sr-only">Wird geladen</span>
    <span :class="spinnerClass" aria-hidden="true" />
    <p v-if="label" class="text-center text-sm text-muted">
      {{ label }}
    </p>
  </div>
</template>

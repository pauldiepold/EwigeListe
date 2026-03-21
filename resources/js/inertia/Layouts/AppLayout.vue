<script setup lang="ts">
defineProps<{
  title?: string;
}>();
</script>

<template>
  <div class="min-h-screen bg-white text-slate-900">
    <main class="mx-auto max-w-4xl px-4 py-8">
      <header class="mb-6 border-b pb-3">
        <h1 class="text-2xl font-semibold">
          {{ title ?? 'Ewige Liste' }}
        </h1>
      </header>
      <slot />
    </main>
  </div>
</template>

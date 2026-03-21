<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps<{
  message: string;
  timestamp: string;
}>();
</script>

<template>
  <Head title="Inertia Smoke Test" />

  <AppLayout title="Inertia Smoke Test">
    <p class="mb-2">{{ message }}</p>
    <p class="text-sm text-slate-600">Serverzeit: {{ timestamp }}</p>
  </AppLayout>
</template>

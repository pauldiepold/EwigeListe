<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DatenschutzContent from '@/Components/Static/DatenschutzContent.vue';
import StaticArticle from '@/Components/Static/StaticArticle.vue';
</script>

<template>
  <Head title="Datenschutz" />

  <AppLayout title="Datenschutzerklärung">
    <StaticArticle>
      <DatenschutzContent />
    </StaticArticle>
  </AppLayout>
</template>

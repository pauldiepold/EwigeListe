<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

/** Ohne `href` darf der Betrachter das Profil nicht sehen: Es bleibt beim reinen Inhalt. */
defineProps<{
  href: string | null;
}>();
</script>

<template>
  <Link v-if="href" :href="href" class="text-primary hover:underline"><slot /></Link>
  <span v-else><slot /></span>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import GroupList from '@/Components/Groups/GroupList.vue';
import { create as createGroup } from '@/routes/groups';
import { recalculate } from '@/routes/statistics';
import type { GroupIndexRow } from '@/types/groups-index';

defineProps<{
  groups: GroupIndexRow[];
  canRecalculateStatistics: boolean;
}>();

const isRecalculating = ref(false);

function recalculateStatistics(): void {
  router.post(
    recalculate.url(),
    {},
    {
      preserveScroll: true,
      onStart: () => (isRecalculating.value = true),
      onFinish: () => (isRecalculating.value = false),
    },
  );
}
</script>

<template>
  <Head title="Listen" />

  <AppLayout title="Listen">
    <div class="mx-auto w-full max-w-md space-y-6">
      <GroupList :groups="groups" />

      <UButton
        :to="createGroup.url()"
        color="primary"
        icon="i-lucide-plus"
        label="Neue Liste erstellen"
        block
      />

      <UButton
        v-if="canRecalculateStatistics"
        color="neutral"
        variant="outline"
        icon="i-lucide-refresh-cw"
        label="Statistiken neu berechnen"
        block
        :loading="isRecalculating"
        @click="recalculateStatistics"
      />
    </div>
  </AppLayout>
</template>

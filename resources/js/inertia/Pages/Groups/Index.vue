<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GroupList from '@/Components/Groups/GroupList.vue';
import { useAppRoute } from '@/composables/useAppRoute';
import type { GroupIndexRow } from '@/types/groups-index';

defineProps<{
  groups: GroupIndexRow[];
}>();

const { route } = useAppRoute();
</script>

<template>
  <Head title="Listen" />

  <AppLayout title="Listen">
    <div class="mx-auto w-full max-w-md space-y-6">
      <GroupList :groups="groups" />

      <UButton :to="route('groups.create')" color="primary" icon="i-lucide-plus" label="Neue Liste erstellen" block />
    </div>
  </AppLayout>
</template>

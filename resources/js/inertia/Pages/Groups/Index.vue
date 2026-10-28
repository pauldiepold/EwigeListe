<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GroupList from '@/Components/Groups/GroupList.vue';
import { create as createGroup } from '@/routes/groups';
import type { GroupIndexRow } from '@/types/groups-index';

defineProps<{
  groups: GroupIndexRow[];
}>();
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
    </div>
  </AppLayout>
</template>

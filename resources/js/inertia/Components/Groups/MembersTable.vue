<script setup lang="ts">
import type { Component } from 'vue';
import { computed, h, ref, resolveComponent } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { Column, ColumnDef, Row, SortingState } from '@tanstack/vue-table';
import type { GroupMember } from '@/types/groups-show';

const props = defineProps<{
  members: GroupMember[];
  highlightPlayerId: number | null;
}>();

const sorting = ref<SortingState>([{ id: 'games', desc: true }]);

function sortIcon(column: Column<GroupMember, unknown>): string {
  const state = column.getIsSorted();
  if (state === 'asc') {
    return 'i-lucide-arrow-up-narrow-wide';
  }
  if (state === 'desc') {
    return 'i-lucide-arrow-down-narrow-wide';
  }
  return 'i-lucide-arrow-up-down';
}

function sortableHeader(label: string): ColumnDef<GroupMember>['header'] {
  return ({ column }) =>
    h(resolveComponent('UButton') as Component, {
      color: 'neutral',
      variant: 'ghost',
      class: '-mx-2.5',
      label,
      trailingIcon: sortIcon(column),
      onClick: column.getToggleSortingHandler(),
    });
}

const columns: ColumnDef<GroupMember>[] = [
  { id: 'avatar', header: '', enableSorting: false },
  { id: 'name', accessorKey: 'name', header: sortableHeader('Name') },
  { id: 'games', accessorKey: 'games', header: sortableHeader('Spiele') },
  { id: 'points', accessorKey: 'points', header: sortableHeader('Punkte') },
  {
    id: 'pointsPerGame',
    accessorKey: 'pointsPerGame',
    header: sortableHeader('Schnitt'),
    cell: ({ row }) => row.original.pointsPerGame.toFixed(2),
  },
  { id: 'soli', accessorKey: 'soli', header: sortableHeader('Soli') },
];

const tableMeta = computed(() => ({
  class: {
    tr: (row: Row<GroupMember>) =>
      row.original.id === props.highlightPlayerId ? 'bg-primary-light' : '',
  },
}));
</script>

<template>
  <div class="overflow-x-auto">
    <UTable
      v-model:sorting="sorting"
      :data="members"
      :columns="columns"
      :meta="tableMeta"
      :get-row-id="(member: GroupMember) => String(member.id)"
      class="w-full"
    >
      <template #avatar-cell="{ row }">
        <Link :href="row.original.path">
          <UAvatar
            :src="row.original.avatarPath ?? undefined"
            :alt="row.original.name"
            loading="lazy"
            size="md"
          />
        </Link>
      </template>

      <template #name-cell="{ row }">
        <Link :href="row.original.path" class="text-primary hover:underline">
          {{ row.original.name }}
        </Link>
      </template>
    </UTable>
  </div>
</template>

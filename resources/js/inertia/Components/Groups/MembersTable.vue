<script setup lang="ts">
import type { Component } from 'vue';
import { computed, h, ref, resolveComponent } from 'vue';
import type { ColumnDef, Row, SortingState } from '@tanstack/vue-table';
import ProfileLink from '@/Components/Ui/ProfileLink.vue';
import { sortIcon, sortIconClass } from '@/composables/tableSorting';
import type { GroupMember } from '@/types/groups-show';

const props = defineProps<{
  members: GroupMember[];
  highlightPlayerId: number | null;
}>();

const sorting = ref<SortingState>([{ id: 'games', desc: true }]);

function sortableHeader(label: string): ColumnDef<GroupMember>['header'] {
  return ({ column }) =>
    h(resolveComponent('UButton') as Component, {
      color: 'neutral',
      variant: 'ghost',
      size: 'sm',
      class: '-mx-2.5',
      label,
      trailingIcon: sortIcon(column),
      ui: { trailingIcon: sortIconClass(column) },
      onClick: column.getToggleSortingHandler(),
    });
}

const columns: ColumnDef<GroupMember>[] = [
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
      row.original.id === props.highlightPlayerId ? 'bg-primary/15' : '',
  },
}));
</script>

<template>
  <!-- Am Handy bis an den Rand wie die Spieltabelle: hebt das Seiten-Padding auf. -->
  <div class="-mx-4 overflow-x-auto sm:mx-0">
    <UTable
      v-model:sorting="sorting"
      :data="members"
      :columns="columns"
      :meta="tableMeta"
      :get-row-id="(member: GroupMember) => String(member.id)"
      :ui="{
        th: 'px-2 py-2 last:pr-2.5',
        td: 'px-2 py-2 tabular-nums',
      }"
      class="w-full"
    >
      <template #name-cell="{ row }">
        <ProfileLink :href="row.original.path" class="flex items-center gap-2">
          <UAvatar
            :src="row.original.avatarPath ?? undefined"
            :alt="row.original.name"
            loading="lazy"
            size="sm"
          />
          <span class="whitespace-normal">{{ row.original.name }}</span>
        </ProfileLink>
      </template>
    </UTable>
  </div>
</template>

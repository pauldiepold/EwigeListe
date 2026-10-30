<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GroupBadges from '@/Components/Groups/GroupBadges.vue';
import ProfileCharts from '@/Components/Players/ProfileCharts.vue';
import ProfileStatisticsPanel from '@/Components/Players/ProfileStatisticsPanel.vue';
import RoundArchive from '@/Components/Rounds/RoundArchive.vue';
import { useTabInUrlHash } from '@/composables/useTabInUrlHash';
import { show as showGroup } from '@/routes/groups';
import { show as showPlayer } from '@/routes/players';
import { edit as editUser } from '@/routes/users';
import type { PlayerShowProps } from '@/types/players-show';
import type { ArchiveSortColumn } from '@/types/rounds-index';

const props = defineProps<PlayerShowProps>();

const page = usePage();
const isOwnProfile = computed(() => page.props.auth.player?.id === props.player.id);

const title = computed(() => `Profil von ${props.player.name}`);

const tabs = [
  { label: 'Statistiken', icon: 'i-lucide-user', value: 'statistiken' },
  { label: 'Graphen', icon: 'i-lucide-chart-area', value: 'graphen' },
  { label: 'Abzeichen', icon: 'i-lucide-award', value: 'abzeichen' },
  { label: 'Rundenarchiv', icon: 'i-lucide-history', value: 'rundenarchiv' },
  { label: 'Listen', icon: 'i-lucide-list', value: 'listen' },
];

const activeTab = useTabInUrlHash(tabs, 'statistiken', 'Players/Show');

function onGroupChange(groupId: number): void {
  router.visit(showPlayer.url({ player: props.player.id, group: groupId }), {
    preserveScroll: true,
    preserveState: true,
  });
}

function visitArchive(query: {
  sort: ArchiveSortColumn;
  direction: 'asc' | 'desc';
  page: number;
}): void {
  router.visit(
    showPlayer.url({ player: props.player.id, group: props.selectedGroupId }, { query }),
    {
      only: ['rounds', 'archiveSort'],
      preserveScroll: true,
      preserveState: true,
    },
  );
}

function onArchivePageChange(targetPage: number): void {
  visitArchive({
    sort: props.archiveSort.column,
    direction: props.archiveSort.direction,
    page: targetPage,
  });
}

function onArchiveSortChange(column: ArchiveSortColumn, direction: 'asc' | 'desc'): void {
  visitArchive({ sort: column, direction, page: 1 });
}
</script>

<template>
  <Head :title="title" />

  <AppLayout :title="title">
    <div class="mx-auto w-full max-w-5xl space-y-6">
      <div class="flex flex-col items-center gap-3">
        <UAvatar :src="player.avatarPath ?? undefined" :alt="player.name" size="3xl" />
        <UButton
          v-if="player.editableUserId !== null"
          :to="editUser.url(player.editableUserId)"
          variant="outline"
          icon="i-lucide-user-cog"
          label="Konto-Einstellungen"
        />
      </div>

      <UTabs v-model="activeTab" :items="tabs" :content="false" class="w-full" />

      <UFormField v-if="activeTab !== 'listen'" label="Liste">
        <USelect
          :model-value="selectedGroupId"
          :items="groupOptions"
          value-key="id"
          label-key="name"
          class="w-full max-w-md"
          @update:model-value="onGroupChange"
        />
      </UFormField>

      <ProfileStatisticsPanel v-if="activeTab === 'statistiken'" :profile="profile" />

      <template v-else-if="activeTab === 'graphen'">
        <ProfileCharts
          v-if="profile.statistics"
          :key="selectedGroupId"
          :chart-data="profileChart"
        />
        <p v-else class="text-lg text-toned">Statistiken werden ab dem 10. Spiel angezeigt.</p>
      </template>

      <template v-else-if="activeTab === 'abzeichen'">
        <GroupBadges v-if="badges.length > 0" :badges="badges" />
        <p v-else class="text-lg text-toned">Bisher keine Abzeichen auf dieser Liste.</p>
      </template>

      <RoundArchive
        v-else-if="activeTab === 'rundenarchiv'"
        :rounds="rounds"
        :archive-sort="archiveSort"
        :highlight-own-rounds="!isOwnProfile"
        @update:page="onArchivePageChange"
        @update:sort="onArchiveSortChange"
      />

      <ul v-else class="space-y-2">
        <li v-for="group in groupOptions" :key="group.id">
          <Link :href="showGroup.url(group.id)" class="text-primary hover:underline">
            {{ group.name }}
          </Link>
        </li>
      </ul>
    </div>
  </AppLayout>
</template>

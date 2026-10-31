<script setup lang="ts">
import { computed } from 'vue';
import { Head, WhenVisible } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import GamesChart from '@/Components/Groups/GamesChart.vue';
import GamesChartLoading from '@/Components/Groups/GamesChartLoading.vue';
import GroupAdminPanel from '@/Components/Groups/GroupAdminPanel.vue';
import GroupBadges from '@/Components/Groups/GroupBadges.vue';
import GroupMembersPanel from '@/Components/Groups/GroupMembersPanel.vue';
import GroupStatisticsTable from '@/Components/Groups/GroupStatisticsTable.vue';
import RoundArchive from '@/Components/Rounds/RoundArchive.vue';
import { useRoundArchiveVisit } from '@/composables/useRoundArchiveVisit';
import { EWIGE_LISTE } from '@/composables/useRoundListSelection';
import { useTabInUrlHash } from '@/composables/useTabInUrlHash';
import { index as groupsIndex, show as showGroup } from '@/routes/groups';
import type { GroupShowProps } from '@/types/groups-show';

const props = defineProps<GroupShowProps>();

const title = computed(() =>
  props.group.id === EWIGE_LISTE.id ? props.group.name : `Liste - ${props.group.name}`,
);

const tabs = computed(() => [
  { label: 'Mitglieder', icon: 'i-lucide-users', value: 'mitglieder' },
  ...(props.statistics.records.length > 0
    ? [{ label: 'Statistiken', icon: 'i-lucide-chart-area', value: 'statistiken' }]
    : []),
  ...(props.badges.length > 0
    ? [{ label: 'Abzeichen', icon: 'i-lucide-award', value: 'abzeichen' }]
    : []),
  { label: 'Rundenarchiv', icon: 'i-lucide-history', value: 'rundenarchiv' },
  ...(props.canClose ? [{ label: 'Admin', icon: 'i-lucide-settings', value: 'admin' }] : []),
]);

const activeTab = useTabInUrlHash(tabs, 'mitglieder', 'Groups/Show');

const { onArchivePageChange, onArchiveSortChange } = useRoundArchiveVisit(
  () => props.archiveSort,
  (query) => showGroup.url(props.group.id, { query }),
);
</script>

<template>
  <Head :title="title" />

  <AppLayout :title="title">
    <div class="mx-auto w-full max-w-5xl space-y-6">
      <div class="flex items-center justify-between gap-2">
        <UBadge
          v-if="group.closed"
          color="neutral"
          variant="subtle"
          icon="i-lucide-lock"
          label="Geschlossen"
        />
        <UButton
          :to="groupsIndex.url()"
          color="neutral"
          variant="ghost"
          icon="i-lucide-list"
          label="Alle Listen"
          class="ml-auto"
        />
      </div>

      <UTabs v-model="activeTab" :items="tabs" :content="false" class="w-full" />

      <GroupMembersPanel
        v-if="activeTab === 'mitglieder'"
        :group-id="group.id"
        :members="members"
        :can-join="canJoin"
        :can-leave="canLeave"
      />

      <section v-else-if="activeTab === 'statistiken'" class="space-y-8">
        <div>
          <h2 class="mb-4 text-xl font-semibold text-highlighted">Rekorde</h2>
          <GroupStatisticsTable :rows="statistics.records" />
        </div>

        <div>
          <h2 class="mb-4 text-xl font-semibold text-highlighted">Statistiken</h2>
          <GroupStatisticsTable :rows="statistics.stats" />
        </div>

        <div v-if="hasGamesChart">
          <h2 class="mb-4 text-xl font-semibold text-highlighted">Anzahl der Spiele</h2>
          <WhenVisible data="gamesChart">
            <template #fallback>
              <GamesChartLoading />
            </template>

            <GamesChart v-if="gamesChart" :chart-data="gamesChart" />
          </WhenVisible>
        </div>
      </section>

      <GroupBadges v-else-if="activeTab === 'abzeichen'" :badges="badges" />

      <RoundArchive
        v-else-if="activeTab === 'rundenarchiv'"
        :rounds="rounds"
        :archive-sort="archiveSort"
        @update:page="onArchivePageChange"
        @update:sort="onArchiveSortChange"
      />

      <GroupAdminPanel
        v-else-if="activeTab === 'admin'"
        :group-id="group.id"
        :closed="group.closed"
      />
    </div>
  </AppLayout>
</template>

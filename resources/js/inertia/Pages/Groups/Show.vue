<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DeferredGamesChart from '@/Components/Groups/DeferredGamesChart.vue';
import GroupBadges from '@/Components/Groups/GroupBadges.vue';
import GroupStatisticsTable from '@/Components/Groups/GroupStatisticsTable.vue';
import MembersTable from '@/Components/Groups/MembersTable.vue';
import RoundArchive from '@/Components/Rounds/RoundArchive.vue';
import { EWIGE_LISTE } from '@/composables/useRoundListSelection';
import { close, index as groupsIndex, join, leave, show as showGroup } from '@/routes/groups';
import type { GroupShowProps } from '@/types/groups-show';
import type { ArchiveSortColumn } from '@/types/rounds-index';

const props = defineProps<GroupShowProps>();

const page = usePage();
const authPlayerId = computed(() => page.props.auth.player?.id ?? null);

const title = computed(() =>
  props.group.id === EWIGE_LISTE.id ? props.group.name : `Liste - ${props.group.name}`,
);
const hasPlayedGames = computed(() => props.members.some((member) => member.games > 0));
const onlyMember = computed(() => (props.members.length === 1 ? props.members[0] : null));

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

const activeTab = ref('mitglieder');

onMounted(() => {
  const tabFromHash = window.location.hash.slice(1);
  if (tabs.value.some((tab) => tab.value === tabFromHash)) {
    activeTab.value = tabFromHash;
  }
});

/** `replaceState` behält den History-State von Inertia, ein neuer Eintrag entsteht nicht. */
watch(activeTab, (tab) => {
  window.history.replaceState(window.history.state, '', `#${tab}`);
});

function joinGroup(): void {
  router.post(join.url(props.group.id), {}, { preserveScroll: true, preserveState: true });
}

function leaveGroup(): void {
  router.post(leave.url(props.group.id), {}, { preserveScroll: true, preserveState: true });
}

function setClosed(closed: boolean): void {
  router.patch(
    close.url(props.group.id),
    { closed },
    { preserveScroll: true, preserveState: true },
  );
}

function visitArchive(query: {
  sort: ArchiveSortColumn;
  direction: 'asc' | 'desc';
  page: number;
}): void {
  router.visit(`${showGroup.url(props.group.id, { query })}#rundenarchiv`, {
    only: ['rounds', 'archiveSort'],
    preserveScroll: true,
    preserveState: true,
  });
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

      <section v-if="activeTab === 'mitglieder'" class="space-y-6">
        <div v-if="canJoin || canLeave" class="flex justify-center gap-2">
          <UButton v-if="canJoin" variant="outline" label="Liste beitreten" @click="joinGroup" />
          <UButton
            v-if="canLeave"
            variant="outline"
            label="Aus Liste austreten"
            @click="leaveGroup"
          />
        </div>

        <template v-if="hasPlayedGames">
          <MembersTable :members="members" :highlight-player-id="authPlayerId" />
          <p class="text-muted">{{ members.length }} Mitglieder</p>
        </template>
        <p v-else-if="onlyMember && onlyMember.id === authPlayerId" class="text-lg text-toned">
          Nur du bist bisher Mitglied dieser Liste.
        </p>
        <p v-else-if="onlyMember" class="text-lg text-toned">
          Bisher ist nur
          <Link :href="onlyMember.path" class="text-primary hover:underline">{{
            onlyMember.surname
          }}</Link>
          Mitglied dieser Liste.
        </p>
        <p v-else class="text-lg text-toned">Bisher wurde keine Runde auf dieser Liste gespielt.</p>
      </section>

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
          <DeferredGamesChart prop="gamesChart" :chart-data="gamesChart" />
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

      <section v-else-if="activeTab === 'admin'" class="mx-auto max-w-sm space-y-4">
        <template v-if="!group.closed">
          <p>Du kannst diese Liste <b>schließen</b>.</p>
          <ul class="list-outside list-disc space-y-1 pl-6">
            <li>Danach können <b>keine</b> neuen Runden hinzugefügt werden</li>
            <li>Mitglieder können <b>nicht</b> mehr beitreten oder austreten</li>
            <li>Anschließend kann die Liste wieder geöffnet werden</li>
          </ul>
          <UButton label="Liste schließen" @click="setClosed(true)" />
        </template>
        <template v-else>
          <p>Du kannst diese geschlossene Liste wieder <b>öffnen</b>.</p>
          <ul class="list-outside list-disc space-y-1 pl-6">
            <li>Danach können wieder neue Runden hinzugefügt werden</li>
            <li>Mitglieder können wieder beitreten und austreten</li>
          </ul>
          <UButton label="Liste öffnen" @click="setClosed(false)" />
        </template>
      </section>
    </div>
  </AppLayout>
</template>

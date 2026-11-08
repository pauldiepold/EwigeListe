<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import type { ButtonProps } from '@nuxt/ui';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProfileBadgeCalendar from '@/Components/Players/ProfileBadgeCalendar.vue';
import ProfileCharts from '@/Components/Players/ProfileCharts.vue';
import ProfileStatisticsPanel from '@/Components/Players/ProfileStatisticsPanel.vue';
import RoundArchive from '@/Components/Rounds/RoundArchive.vue';
import { useRoundArchiveVisit } from '@/composables/useRoundArchiveVisit';
import { useTabInUrlHash } from '@/composables/useTabInUrlHash';
import { show as showGroup } from '@/routes/groups';
import { show as showPlayer } from '@/routes/players';
import { edit as editUser } from '@/routes/users';
import type { PlayerShowProps } from '@/types/players-show';

const props = defineProps<PlayerShowProps>();

const page = usePage();
const isOwnProfile = computed(() => page.props.auth.player?.id === props.player.id);

const title = computed(() => `Profil von ${props.player.fullName}`);

/** Am Desktop neben dem Titel, darunter unter dem Avatar, weil der Name sonst keinen Platz hat. */
const accountSettingsButton = computed<ButtonProps | null>(() =>
  props.player.editableUserId === null
    ? null
    : {
        to: editUser.url(props.player.editableUserId),
        label: 'Konto-Einstellungen',
        icon: 'i-lucide-user-cog',
        color: 'neutral',
        variant: 'subtle',
        size: 'sm',
      },
);

const headerLinks = computed<ButtonProps[]>(() =>
  accountSettingsButton.value === null
    ? []
    : [{ ...accountSettingsButton.value, class: 'hidden lg:inline-flex' }],
);

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

const { onArchivePageChange, onArchiveSortChange } = useRoundArchiveVisit(
  () => props.archiveSort,
  (query) => showPlayer.url({ player: props.player.id, group: props.selectedGroupId }, { query }),
);
</script>

<template>
  <Head :title="title" />

  <AppLayout :title="title" :header-links="headerLinks">
    <div class="mx-auto w-full max-w-5xl space-y-6">
      <div class="flex flex-col items-center gap-3">
        <UModal
          v-if="player.avatarPath && player.avatarFullPath"
          :title="player.fullName"
          :ui="{ content: 'max-w-2xl' }"
        >
          <button
            type="button"
            class="cursor-zoom-in rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
            aria-label="Profilbild vergrößern"
          >
            <img
              :src="player.avatarPath"
              :alt="player.fullName"
              class="size-36 rounded-full object-cover sm:size-44"
            />
          </button>

          <template #body>
            <img :src="player.avatarFullPath" :alt="player.fullName" class="w-full rounded-md" />
          </template>
        </UModal>
        <img
          v-else-if="player.avatarPath"
          :src="player.avatarPath"
          :alt="player.fullName"
          class="size-36 rounded-full object-cover sm:size-44"
        />
        <UAvatar v-else :alt="player.fullName" class="size-36 text-5xl sm:size-44" />
        <UButton v-if="accountSettingsButton" v-bind="accountSettingsButton" class="lg:hidden" />
      </div>

      <UTabs v-model="activeTab" :items="tabs" :content="false" class="w-full" />

      <UFormField
        v-if="activeTab !== 'listen'"
        label="Liste"
        :class="{
          'mx-auto max-w-md': activeTab === 'statistiken',
          'mx-auto max-w-2xl': activeTab === 'abzeichen',
        }"
      >
        <USelect
          :model-value="selectedGroupId"
          :items="groupOptions"
          value-key="id"
          label-key="name"
          class="w-full max-w-md"
          @update:model-value="onGroupChange"
        />
      </UFormField>

      <ProfileStatisticsPanel
        v-if="activeTab === 'statistiken'"
        :profile="profile"
        :badge-count="badgeCount"
      />

      <template v-else-if="activeTab === 'graphen'">
        <ProfileCharts
          v-if="profile.statistics"
          :key="selectedGroupId"
          :chart-data="profileChart"
        />
        <p v-else class="text-lg text-toned">Statistiken werden ab dem 10. Spiel angezeigt.</p>
      </template>

      <template v-else-if="activeTab === 'abzeichen'">
        <ProfileBadgeCalendar v-if="badges.length > 0" :years="badges" />
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

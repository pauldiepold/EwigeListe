<script setup lang="ts">
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import MembersTable from '@/Components/Groups/MembersTable.vue';
import ProfileLink from '@/Components/Ui/ProfileLink.vue';
import { join, leave } from '@/routes/groups';
import type { GroupMember } from '@/types/groups-show';

const props = defineProps<{
  groupId: number;
  members: GroupMember[];
  canJoin: boolean;
  canLeave: boolean;
}>();

const page = usePage();
const authPlayerId = computed(() => page.props.auth.player?.id ?? null);

const hasPlayedGames = computed(() => props.members.some((member) => member.games > 0));
const onlyMember = computed(() => (props.members.length === 1 ? props.members[0] : null));

function joinGroup(): void {
  router.post(join.url(props.groupId), {}, { preserveScroll: true, preserveState: true });
}

function leaveGroup(): void {
  router.post(leave.url(props.groupId), {}, { preserveScroll: true, preserveState: true });
}
</script>

<template>
  <section class="space-y-6">
    <div v-if="canJoin || canLeave" class="flex justify-center gap-2">
      <UButton v-if="canJoin" variant="outline" label="Liste beitreten" @click="joinGroup" />
      <UButton v-if="canLeave" variant="outline" label="Aus Liste austreten" @click="leaveGroup" />
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
      <ProfileLink :href="onlyMember.path">{{ onlyMember.surname }}</ProfileLink>
      Mitglied dieser Liste.
    </p>
    <p v-else class="text-lg text-toned">Bisher wurde keine Runde auf dieser Liste gespielt.</p>
  </section>
</template>

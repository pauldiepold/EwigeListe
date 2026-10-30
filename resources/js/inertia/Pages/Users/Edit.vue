<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { show as showPlayer } from '@/actions/App/Http/Controllers/PlayerController';
import UserAvatarForm from '@/Components/Users/UserAvatarForm.vue';
import UserDefaultListsForm from '@/Components/Users/UserDefaultListsForm.vue';
import UserEmailForm from '@/Components/Users/UserEmailForm.vue';
import UserNameForm from '@/Components/Users/UserNameForm.vue';
import UserPasswordForm from '@/Components/Users/UserPasswordForm.vue';
import type { EditUser, EditUserProfile } from '@/types/user-edit';

defineProps<{
  user: EditUser;
  profiles: EditUserProfile[];
}>();

const tabs = [
  { label: 'Listen', icon: 'i-lucide-list', value: 'listen', slot: 'listen' as const },
  { label: 'Name', icon: 'i-lucide-user', value: 'name', slot: 'name' as const },
  { label: 'E-Mail', icon: 'i-lucide-at-sign', value: 'email', slot: 'email' as const },
  { label: 'Passwort', icon: 'i-lucide-key-round', value: 'passwort', slot: 'passwort' as const },
];
</script>

<template>
  <Head title="Kontoeinstellungen" />

  <AppLayout title="Kontoeinstellungen">
    <div class="mx-auto w-full max-w-md space-y-6">
      <UButton
        :to="showPlayer.url({ player: user.player_id })"
        color="neutral"
        variant="ghost"
        icon="i-lucide-arrow-left"
        label="Zurück zum Profil"
      />

      <UCard>
        <UTabs :items="tabs" default-value="name" :unmount-on-hide="false">
          <template #listen>
            <UserDefaultListsForm :user="user" :profiles="profiles" class="mt-4" />
          </template>

          <template #name>
            <div class="mt-4 space-y-6">
              <UserAvatarForm :user="user" />
              <USeparator />
              <UserNameForm :user="user" />
            </div>
          </template>

          <template #email>
            <UserEmailForm :user="user" class="mt-4" />
          </template>

          <template #passwort>
            <UserPasswordForm :user="user" class="mt-4" />
          </template>
        </UTabs>
      </UCard>
    </div>
  </AppLayout>
</template>

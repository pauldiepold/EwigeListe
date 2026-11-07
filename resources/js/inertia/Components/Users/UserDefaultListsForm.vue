<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { updateListen } from '@/routes/users';
import type { EditUser, EditUserProfile } from '@/types/user-edit';

const props = defineProps<{
  user: EditUser;
  profiles: EditUserProfile[];
}>();

const form = useForm({
  default_profiles: props.profiles
    .filter((profile) => profile.default)
    .map((profile) => profile.id),
});

function isDefault(profile: EditUserProfile): boolean {
  return form.default_profiles.includes(profile.id);
}

function setDefault(profile: EditUserProfile, value: boolean): void {
  form.default_profiles = value
    ? [...form.default_profiles, profile.id]
    : form.default_profiles.filter((id) => id !== profile.id);
}

function submit(): void {
  form.patch(updateListen.url(props.user.id), {
    preserveState: true,
    preserveScroll: true,
  });
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="submit">
    <p class="text-sm text-muted">
      Jede deiner Listen kann als Standardliste ausgewählt werden. Sie wird beim Start einer neuen
      Runde automatisch aktiviert.
    </p>

    <ul class="divide-y divide-default">
      <li
        v-for="profile in profiles"
        :key="profile.id"
        class="flex items-center justify-between gap-3 py-2"
      >
        <span>{{ profile.name }}</span>
        <USwitch
          v-if="profile.selectable"
          :model-value="isDefault(profile)"
          :aria-label="`${profile.name} als Standardliste`"
          :disabled="form.processing"
          @update:model-value="setDefault(profile, $event)"
        />
        <UIcon
          v-else-if="profile.closed"
          name="i-lucide-lock"
          class="size-4 text-muted"
          aria-label="Geschlossene Liste"
        />
      </li>
    </ul>

    <UButton
      type="submit"
      color="primary"
      label="Standardlisten speichern"
      block
      :loading="form.processing"
    />
  </form>
</template>

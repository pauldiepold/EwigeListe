<script setup lang="ts">
import { computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import RoundListSelection from '@/Components/Rounds/RoundListSelection.vue';
import { show as showGroup } from '@/routes/groups';
import { update as updateRound } from '@/routes/rounds';
import type { RoundList } from '@/types/round-show';

const props = defineProps<{
  roundId: number;
  lists: RoundList[];
  canUpdate: boolean;
}>();

const editing = ref(false);

const form = useForm({
  groups: [] as number[],
});

const selectedLists = computed(() => props.lists.filter((list) => list.selected));
const validationError = computed(() => Object.values(form.errors)[0]);

function isSelected(listId: number): boolean {
  return form.groups.includes(listId);
}

function isLocked(listId: number): boolean {
  return props.lists.some((list) => list.id === listId && list.locked);
}

function setSelected(listId: number, selected: boolean): void {
  form.groups = selected
    ? [...form.groups, listId]
    : form.groups.filter((selectedId) => selectedId !== listId);
}

function startEditing(): void {
  form.groups = selectedLists.value.map((list) => list.id);
  form.clearErrors();
  editing.value = true;
}

function save(): void {
  form.patch(updateRound.url(props.roundId), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      editing.value = false;
    },
  });
}
</script>

<template>
  <section class="mx-auto max-w-sm space-y-4">
    <template v-if="editing">
      <UAlert
        v-if="validationError"
        color="error"
        variant="subtle"
        icon="i-lucide-circle-alert"
        :title="validationError"
      />

      <RoundListSelection
        :lists="lists"
        :is-selected="isSelected"
        :is-locked="isLocked"
        :selected-count="form.groups.length"
        @toggle="setSelected"
      />

      <p class="text-sm text-muted">
        Eine Runde kann nur Listen hinzugefügt werden, in denen mindestens eine der Personen
        Mitglied ist. Geschlossene Listen bleiben unverändert.
      </p>

      <div class="flex justify-center gap-3">
        <UButton color="neutral" variant="outline" label="Abbrechen" @click="editing = false" />
        <UButton
          color="primary"
          label="Listen speichern"
          :loading="form.processing"
          @click="save"
        />
      </div>
    </template>

    <template v-else>
      <ul class="space-y-2 text-center">
        <li v-for="list in selectedLists" :key="list.id">
          <Link :href="showGroup.url(list.id)" class="font-semibold text-primary hover:underline">
            {{ list.name }}
          </Link>
        </li>
      </ul>

      <div v-if="canUpdate" class="flex justify-center">
        <UButton
          color="neutral"
          variant="outline"
          label="Listen bearbeiten"
          @click="startEditing"
        />
      </div>
    </template>
  </section>
</template>

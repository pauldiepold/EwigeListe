<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { close } from '@/routes/groups';

const props = defineProps<{
  groupId: number;
  closed: boolean;
}>();

function setClosed(closed: boolean): void {
  router.patch(close.url(props.groupId), { closed }, { preserveScroll: true, preserveState: true });
}
</script>

<template>
  <section class="mx-auto max-w-sm space-y-4">
    <template v-if="!closed">
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
</template>

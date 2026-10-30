<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { store as storeQuickRegister } from '@/routes/register/quick';

/**
 * Schnellregistrierung direkt in „Neue Runde“. Als Modal statt eigener Seite, damit die
 * bisherige Auswahl erhalten bleibt (`preserveState`); der neue Spieler kommt per Flash
 * `addedPlayerId` zurück und wird per `registered` an die Page gemeldet.
 */
const emit = defineEmits<{
  registered: [playerId: number];
}>();

const open = ref(false);

const form = useForm({
  surname: '',
  name: '',
});

function submit(): void {
  form.post(storeQuickRegister.url(), {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      const playerId = page.flash.addedPlayerId;
      form.reset();
      open.value = false;
      if (playerId !== undefined) {
        emit('registered', playerId);
      }
    },
  });
}

function close(): void {
  open.value = false;
}
</script>

<template>
  <UModal
    v-model:open="open"
    title="Neue Person registrieren"
    description="Legt einen neuen Spieler an und fügt ihn dieser Runde hinzu."
    :ui="{ content: 'top-4 translate-y-0 sm:top-16' }"
  >
    <UButton
      color="secondary"
      variant="outline"
      icon="i-lucide-user-plus"
      label="Neue Person registrieren"
    />

    <template #body>
      <form id="quick-register-form" class="space-y-4" @submit.prevent="submit">
        <UFormField label="Vorname" :error="form.errors.surname" required>
          <UInput
            v-model="form.surname"
            name="surname"
            autocomplete="off"
            autofocus
            class="w-full"
            :disabled="form.processing"
          />
        </UFormField>

        <UFormField label="Nachname" :error="form.errors.name" required>
          <UInput
            v-model="form.name"
            name="name"
            autocomplete="off"
            class="w-full"
            :disabled="form.processing"
          />
        </UFormField>
      </form>
    </template>

    <template #footer>
      <div class="flex w-full justify-end gap-3">
        <UButton color="neutral" variant="outline" label="Abbrechen" @click="close" />
        <UButton
          type="submit"
          form="quick-register-form"
          color="primary"
          icon="i-lucide-user-plus"
          label="Registrieren"
          :loading="form.processing"
        />
      </div>
    </template>
  </UModal>
</template>

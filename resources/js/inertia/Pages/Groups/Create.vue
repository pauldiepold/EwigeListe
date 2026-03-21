<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAppRoute } from '@/composables/useAppRoute';

const { route } = useAppRoute();

const form = useForm({
  name: '',
});

function submit(): void {
  form.post(route('groups.store'));
}
</script>

<template>
  <Head title="Liste erstellen" />

  <AppLayout title="Liste erstellen">
    <div class="mx-auto w-full max-w-md space-y-6">
      <UButton
        :to="route('groups.index')"
        color="neutral"
        variant="ghost"
        icon="i-lucide-arrow-left"
        label="Zurück zu den Listen"
      />

      <UCard>
        <template #header>
          <h2 class="text-lg font-semibold">Neue Liste</h2>
        </template>

        <form class="space-y-4" @submit.prevent="submit">
          <UFormField label="Name der Liste" :error="form.errors.name">
            <UInput
              v-model="form.name"
              name="name"
              placeholder="Listenname"
              autocomplete="off"
              :disabled="form.processing"
            />
          </UFormField>

          <UButton
            type="submit"
            color="primary"
            icon="i-lucide-plus"
            label="Liste erstellen"
            block
            :loading="form.processing"
          />
        </form>
      </UCard>
    </div>
  </AppLayout>
</template>

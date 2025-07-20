<script setup lang="ts">
import { useHead } from '@unhead/vue';

import InAppLayout from "@/components/layout/InAppLayout.vue";
import BackButton from "@/components/navigation/BackButton.vue";

useHead({
  title: 'Beatly | Gestion des albums',
})
</script>

<template>
  <InAppLayout type="admin" padding="p-10">
    <div>
      <BackButton to="/admin" />
      <h1 class="text-3xl font-bold mb-6">Gestion des musiques</h1>
    </div>
  </InAppLayout>
</template>

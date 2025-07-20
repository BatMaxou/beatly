<script setup lang="ts">
import InAppLayout from "@/components/layout/InAppLayout.vue";
import BackButton from "@/components/navigation/BackButton.vue";
</script>

<template>
  <InAppLayout type="admin" padding="p-10">
    <div>
      <BackButton to="/admin" />
      <h1 class="text-3xl font-bold mb-6">Gestion des utilisateurs</h1>
    </div>
  </InAppLayout>
</template>

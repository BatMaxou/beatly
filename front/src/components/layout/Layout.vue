<script setup lang="ts">
defineProps({
  currentView: {
    type: Object,
    required: true,
  },
});
import UiNavigateButton from "@/components/buttons/UiNavigateButton.vue";
</script>

<template>
  <div class="relative min-h-screen">
    <main>
      <component :is="currentView" />
    </main>
    <UiNavigateButton />
  </div>
</template>

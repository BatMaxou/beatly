<script setup lang="ts">
import UiNavigateButton from "@/components/buttons/UiNavigateButton.vue";
</script>

<template>
  <div class="relative min-h-screen">
    <main>
      <slot></slot>
    </main>
    <UiNavigateButton />
  </div>
</template>

<script setup>
defineProps({
  currentView: {
    type: Object,
    required: true
  }
})
import UiNavigateButton from '@/components/buttons/UiNavigateButton.vue'
</script>

<template>
  <div class="layout">
    <main>
        {{ console.log(currentView) }}
      <component :is="currentView" />
    </main>
    <UiNavigateButton />
  </div>
</template>

<style scoped>
.layout {
  position: relative;
  min-height: 100vh;
}
</style>

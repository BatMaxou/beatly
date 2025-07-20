<script setup lang="ts">
import { useRouter } from "vue-router";

interface Props {
  to?: string;
  label?: string;
}

const props = withDefaults(defineProps<Props>(), {
  label: "Retour",
});

const router = useRouter();

const goBack = () => {
  if (props.to) {
    router.push(props.to);
  } else {
    router.back();
  }
};
</script>

<template>
  <button
    @click="goBack"
    class="flex items-center space-x-2 px-3 py-2 text-white hover:text-gray-300 transition-colors mb-6 bg-purple-600 rounded-lg"
  >
    <span>←</span>
    <span>{{ label }}</span>
  </button>
</template>

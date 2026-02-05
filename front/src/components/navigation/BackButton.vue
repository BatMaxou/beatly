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
  if (document.referrer.includes("/admin")) {
    router.back();
    return;
  }
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
    class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors"
  >
    <span>←</span>
    <span>{{ label }}</span>
  </button>
</template>

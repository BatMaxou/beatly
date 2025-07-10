<script setup lang="ts">
import loadingIcon from "@/assets/icons/loading-light.svg";

  const props = defineProps({
    label: {
      type: String,
      required: true
    },
    icon: {
      type: String,
      default: null,
    },
    type: {
      type: String as () => "button" | "submit" | "reset",
      default: "button",
    },
    loading: {
      type: Boolean,
      default: false,
    },
  });
</script>

<template>
  <button
    class=" bg-[#B00D70] rounded-3xl px-10 py-2 hover:bg-[#940a5e] transition flex items-center justify-center"
    :type=props.type
    :disabled="props.loading"
  >
    <img v-if="props.icon && !props.loading" :src="props.icon" class="w-6 h-6 inline-block mr-2" :alt="props.label" />
    <span v-if="!props.loading" class="uppercase text-sm font-bold text-white">{{props.label}}</span>

    <img v-if="props.loading" :src="loadingIcon" alt="Chargement" class="h-4 w-4 animate-spin" />
  </button>
</template>
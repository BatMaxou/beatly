<script setup>
import { ref, computed } from "vue";
import playDark from "@/assets/icons/play-dark.svg";
import playLight from "@/assets/icons/play-light.svg";
import pauseDark from "@/assets/icons/pause-dark.svg";
import pauseLight from "@/assets/icons/pause-light.svg";

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: "default",
    validator: (value) => ["small", "default", "large"].includes(value),
  },
});

const emit = defineEmits(["toggleFavorite"]);

const isHovered = ref(false);

const toggleFavorite = () => {
  if (!props.disabled) {
    emit("toggleFavorite");
  }
};

// Classes dynamiques basées sur les props
const buttonClasses = computed(() => {
  const baseClasses =
    "w-16 h-16 flex items-center justify-center rounded-full transition-all duration-300 focus:outline-none box-border dark:hover:bg-gray-200";

  // Classes d'état
  let stateClasses = "";

  if (props.disabled) {
    stateClasses =
      "bg-white text-gray-400 border border-gray-400 cursor-not-allowed dark:text-gray-500 dark:border-gray-600";
  } else if (props.isActive) {
    stateClasses = "bg-white hover:bg-white/10 border border-white";
  } else {
    stateClasses = "bg-white hover:bg-white/10 border border-white";
  }

  return `${baseClasses} ${stateClasses}`;
});

const heartClasses = computed(() => {
  const baseClasses = "transition-all duration-300 w-8 h-8";

  // Classes d'état
  let stateClasses = "";

  if (props.disabled) {
    stateClasses = "opacity-60 dark:opacity-50";
  } else if (props.isActive) {
    stateClasses = "invert-0";
  } else {
    stateClasses = "invert-0";
  }

  return `${baseClasses} ${stateClasses}`;
});
</script>

<template>
  <button
    :class="buttonClasses"
    @click="toggleFavorite"
    @mouseenter="isHovered = true"
    @mouseleave="isHovered = false"
    type="button"
  >
    <img
      :src="
        props.isActive ? (isHovered ? playLight : playDark) : isHovered ? pauseLight : pauseDark
      "
      :alt="props.isActive ? `play` : `pause`"
      :class="heartClasses"
    />
  </button>
</template>

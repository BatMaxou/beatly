<script setup>
import { ref, computed } from "vue";
import addToFavActive from "@/assets/icons/add-to-fav-active.svg";
import addToFavInactive from "@/assets/icons/add-to-fav-inactive.svg";

const props = defineProps({
  isFavorite: {
    type: Boolean,
    default: false,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: "default", // 'small', 'default', 'large'
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
    "flex items-center justify-center gap-2.5 rounded-full transition-all duration-300 focus:outline-none box-border";

  // Classes de taille
  const sizeClasses = {
    small: "px-6 py-2 text-sm",
    default: "px-10 py-3 text-base",
    large: "px-10 py-3 text-lg",
  };

  // Classes d'état
  let stateClasses = "";

  if (props.disabled) {
    stateClasses =
      "bg-transparent text-gray-400 border border-gray-400 cursor-not-allowed dark:text-gray-500 dark:border-gray-600";
  } else if (props.isFavorite) {
    stateClasses =
      "bg-white text-black hover:bg-gray-100 dark:bg-white dark:text-black dark:hover:bg-gray-200";
  } else {
    stateClasses =
      "bg-transparent text-white hover:bg-white/10 border border-white dark:text-white dark:border-white dark:hover:bg-white/10";
  }

  return `${baseClasses} ${sizeClasses[props.size]} ${stateClasses}`;
});

const heartClasses = computed(() => {
  const baseClasses = "transition-all duration-300";

  // Classes de taille
  const sizeClasses = {
    small: "w-4 h-4",
    default: "w-5 h-5",
    large: "w-6 h-6",
  };

  // Classes d'état
  let stateClasses = "";

  if (props.disabled) {
    stateClasses = "opacity-60 dark:opacity-50";
  } else if (props.isFavorite) {
    stateClasses = "invert-0";
  } else {
    stateClasses = "invert-0";
  }

  return `${baseClasses} ${sizeClasses[props.size]} ${stateClasses}`;
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
    <!-- Icône de cœur -->
    <img
      :src="props.isFavorite ? addToFavActive : addToFavInactive"
      alt="Heart Icon"
      :class="heartClasses"
    />

    <!-- Texte du bouton -->
    <span class="ml-1">{{ props.isFavorite ? "Retirer des favoris" : "Ajouter aux favoris" }}</span>
  </button>
</template>

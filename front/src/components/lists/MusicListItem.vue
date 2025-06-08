<script setup>
import { defineProps, defineEmits, ref, computed } from "vue";
import { convertDurationInMinutes } from "@/sharedFunctions.vue";
import MusicPlayButton from "@/components/lists/MusicPlayButton.vue";

const props = defineProps({
  music: {
    type: Object,
    required: true,
  },
  index: {
    type: Number,
    required: true,
  },
  isPlaying: {
    type: Boolean,
    default: false,
  },
  customStyles: {
    type: Object,
    default: () => ({}),
  },
  theme: {
    type: String,
    default: "dark",
  },
});

const emit = defineEmits(["toggle-play"]);

const isHovered = ref(false);

const formattedDuration = computed(() => {
  return convertDurationInMinutes(props.music.duration);
});

const handleMouseEnter = () => {
  isHovered.value = true;
};

const handleMouseLeave = () => {
  isHovered.value = false;
};

const handleRowClick = () => {
  emit("toggle-play", props.music.position);
};

const textColor = computed(() => {
  return props.theme === "dark" ? "text-black" : "text-white";
});
</script>

<template>
  <div
    class="flex flex-row justify-between items-center h-10 px-4 transition-colors duration-200 ease-in-out cursor-pointer border-b border-gray-200/20 hover:bg-black/80 hover:text-white"
    :class="{ 'bg-black/80 text-white': isPlaying }"
    @mouseenter="handleMouseEnter"
    @mouseleave="handleMouseLeave"
    @click="handleRowClick"
  >
    <div class="flex flex-row items-center gap-2">
      <div class="flex items-center justify-center w-8 min-w-8">
        <span v-if="!isHovered && !isPlaying" class="text-gray-500 font-medium">
          {{ index + 1 }}
        </span>
        <MusicPlayButton
          v-else
          :musicId="music.position"
          :isPlaying="isPlaying"
          @toggle-play="$emit('toggle-play', music.position)"
        />
      </div>
      <span class="text-gray-400 mx-2"> | </span>
      <span class="font-medium" :class="textColor">{{ music.title }}</span>
    </div>
    <div class="text-gray-500 font-normal">
      <h4 class="m-0 text-sm">{{ formattedDuration }}</h4>
    </div>
  </div>
</template>

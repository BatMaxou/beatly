<script setup lang="ts">
import { defineProps } from "vue";
import type { Artist } from "@/utils/types";
import defaultCover from "@/assets/images/default-cover.png";
import { useRouter } from "vue-router";
import { ressourceUrl } from "@/utils/tools";

const props = defineProps({
  artist: {
    type: Object as () => Artist,
    required: true,
  },
});

const router = useRouter();

const goToArtist = () => {
  router.push(`/artiste/${props.artist.id}`);
};
</script>

<template>
  <div class="flex flex-wrap my-4 max-w-[160px] cursor-pointer" @click="goToArtist()">
    <div class="relative w-full overflow-hidden">
      <img
        :src="artist.avatar ? ressourceUrl + artist.avatar : defaultCover"
        :alt="artist.name"
        class="w-full block transition-transform duration-300 ease-in-out rounded-full"
      />
    </div>
    <div class="w-full mt-2">
      <h4>{{ artist.name }}</h4>
    </div>
  </div>
</template>

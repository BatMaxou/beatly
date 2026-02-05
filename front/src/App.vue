<script setup lang="ts">
import { onBeforeUnmount, onMounted } from "vue";
import { useHead } from '@unhead/vue'

import AudioPlayer from "./components/audio/AudioPlayer.vue";
import { usePlayerStore } from "./stores/player";

useHead({
  title: 'Beatly',
  meta: [
    {
      name: 'description',
      content: 'Beatly - Plateforme gratuite d\'écoute et de partage de musique. Découvrez de nouveaux artistes, créez vos playlists et partagez vos morceaux préférés.',
    },
    {
      name: 'keywords',
      content: 'beatly, musique gratuite, streaming musical, plateforme musicale, écoute musique en ligne, partage musique, playlist gratuite, découverte musicale, artistes émergents, création musicale, communauté musicale, musique indie, musique pop, musique rock, musique électronique, musique hip-hop, musique jazz, musique classique, musique du monde, musique alternative, musique acoustique, musique instrumentale, musique pour se détendre, musique pour travailler, musique pour étudier, musique pour faire du sport',
    },
    {
      property: 'og:title',
      content: 'Beatly - Votre plateforme musicale gratuite pour découvrir et partager de la musique',
    },
    {
      property: 'og:description',
      content: 'Rejoignez Beatly, la communauté musicale gratuite où vous pouvez écouter des milliers de morceaux, découvrir des artistes émergents et partager vos créations musicales.',
    },
    {
      property: 'og:image',
      content: 'https://beatly.com/banner.png',
    },
    {
      name: 'twitter:title',
      content: 'Beatly 🎵 Musique gratuite, partage et découverte',
    },
    {
      name: 'twitter:description',
      content: '🎧 Plateforme musicale 100% gratuite | 🚀 Découvrez de nouveaux talents | 📱 Créez et partagez vos playlists',
    },
    {
      name: 'twitter:image',
      content: 'https://beatly.com/banner.png',
    },
    {
      name: 'twitter:card',
      content: 'summary_large_image',
    },
  ]
})

const playerStore = usePlayerStore();
const handleBeforeUnload = () => {
  playerStore.clearQueue();
};

onMounted(() => {
  window.addEventListener("beforeunload", handleBeforeUnload);
});

onBeforeUnmount(() => {
  window.removeEventListener("beforeunload", handleBeforeUnload);
});
</script>

<template>
  <AudioPlayer />

  <router-view />
</template>

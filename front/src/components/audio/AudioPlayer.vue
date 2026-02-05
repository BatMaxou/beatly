<script lang="ts" setup>
import { onMounted, ref, watch } from "vue";
import { usePlayerStore } from "@/stores/player";

const playerStore = usePlayerStore();
const playerElement = ref<HTMLAudioElement | null>(null);

// Synchroniser les changements de fichier musical
watch(
  () => playerStore.musicFile,
  async (newMusicFile) => {
    if (playerElement.value && newMusicFile) {
      // Pause l'audio avant de changer la source pour éviter AbortError
      const wasPlaying = !playerElement.value.paused;

      try {
        playerElement.value.pause();
        playerElement.value.src = newMusicFile;

        // Attendre que l'audio soit prêt avant de potentiellement relancer
        await new Promise<void>((resolve) => {
          const handleCanPlay = () => {
            playerElement.value?.removeEventListener("canplay", handleCanPlay);
            resolve();
          };
          playerElement.value?.addEventListener("canplay", handleCanPlay);
        });

        // Si l'audio était en cours de lecture, le relancer
        if (wasPlaying && playerStore.isPlay) {
          try {
            await playerElement.value.play();
          } catch (error) {
            console.warn("Erreur lors de la relance de la lecture:", error);
          }
        }
      } catch (error) {
        console.warn("Erreur lors du changement de source audio:", error);
      }
    }
  },
);

onMounted(() => {
  if (playerElement.value) {
    playerStore.setAudioPlayer(playerElement.value);

    // Restaurer la source si elle existe
    if (playerStore.musicFile) {
      playerElement.value.src = playerStore.musicFile;
    }
  }
});
</script>

<template>
  <audio
    ref="playerElement"
    class="hidden pointer-events-none"
    src=""
    v-audio
    controls
    preload="auto"
  />
</template>

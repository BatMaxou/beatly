import { usePlayerPreparation } from "@/composables/usePlayerPreparation";
import { usePlayerStore } from "@/stores/player";
import { debounce } from "lodash";

interface ExtendedHTMLAudioElement extends HTMLAudioElement {
  _audioDirectiveCleanup?: () => void;
}

export default {
  mounted(el: HTMLAudioElement) {
    if (el.tagName.toLowerCase() !== "audio") {
      console.warn("L'element fourni n'est pas un element audio.");
      return;
    }

    const playerStore = usePlayerStore();
    const { playNextSong } = usePlayerPreparation();
    const extendedEl = el as ExtendedHTMLAudioElement;

    el.controls = true;

    const handleTimeUpdate = debounce(() => {
      // Mettre à jour le store seulement 5 fois par seconde pour éviter les appels excessifs
      if (!playerStore.isPlayerInteraction) {
        playerStore.setCurrentTime(el.currentTime);
      }
    }, 200); // 200ms = 5 fois par seconde

    const handleLoadedMetadata = () => {
      playerStore.setDuration(el.duration);
    };

    const handleDurationChange = () => {
      if (el) {
        playerStore.setDuration(el.duration);
      }
    };

    const handleCanPlay = () => {
      if (el) {
        playerStore.setDuration(el.duration);
      }
    };

    const handleEnded = async () => {
      if (playerStore.nextMusic) {
        playNextSong();
      } else {
        playerStore.setPause();
      }
    };

    el.addEventListener("loadedmetadata", handleLoadedMetadata);
    el.addEventListener("timeupdate", handleTimeUpdate);
    el.addEventListener("durationchange", handleDurationChange);
    el.addEventListener("canplay", handleCanPlay);
    el.addEventListener("ended", handleEnded);

    extendedEl._audioDirectiveCleanup = () => {
      el.removeEventListener("loadedmetadata", handleLoadedMetadata);
      el.removeEventListener("timeupdate", handleTimeUpdate);
      el.removeEventListener("durationchange", handleDurationChange);
      el.removeEventListener("canplay", handleCanPlay);
      el.removeEventListener("ended", handleEnded);
    };
  },

  unmounted(el: HTMLAudioElement) {
    const extendedEl = el as ExtendedHTMLAudioElement;
    if (extendedEl._audioDirectiveCleanup) {
      extendedEl._audioDirectiveCleanup();
      delete extendedEl._audioDirectiveCleanup;
    }
  },
};

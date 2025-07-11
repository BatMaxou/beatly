import { usePlayerStore } from "@/stores/player";

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
    const extendedEl = el as ExtendedHTMLAudioElement;

    el.controls = true;

    const range = el.parentElement?.querySelector('input[type="range"]');

    if (!range) {
      console.warn("Slider non trouve a cote de l'element audio.");
      return;
    }

    const hasInteractionMethod = typeof playerStore.setIsPlayerInteraction === "function";

    const handleInteractionStart = () => {
      if (hasInteractionMethod) {
        playerStore.setIsPlayerInteraction(true);
      }
    };
    const handleInteractionEnd = () => {
      if (hasInteractionMethod) {
        playerStore.setIsPlayerInteraction(false);
      }
    };

    range.addEventListener("mousedown", handleInteractionStart);
    range.addEventListener("mouseup", handleInteractionEnd);
    range.addEventListener("touchstart", handleInteractionStart);
    range.addEventListener("touchend", handleInteractionEnd);
    range.addEventListener("focus", handleInteractionStart);
    range.addEventListener("blur", handleInteractionEnd);

    const handleLoadedMetadata = () => {
      playerStore.setDuration(el.duration);
    };

    const handleTimeUpdate = () => {
      // Émettre un événement personnalisé pour que le composant parent puisse l'écouter
      el.dispatchEvent(
        new CustomEvent("audio-timeupdate", {
          detail: {
            currentTime: el.currentTime,
            duration: el.duration,
          },
        }),
      );
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

    el.addEventListener("loadedmetadata", handleLoadedMetadata);
    el.addEventListener("timeupdate", handleTimeUpdate);
    el.addEventListener("durationchange", handleDurationChange);
    el.addEventListener("canplay", handleCanPlay);

    extendedEl._audioDirectiveCleanup = () => {
      range?.removeEventListener("mousedown", handleInteractionStart);
      range?.removeEventListener("mouseup", handleInteractionEnd);
      range?.removeEventListener("touchstart", handleInteractionStart);
      range?.removeEventListener("touchend", handleInteractionEnd);
      range?.removeEventListener("focus", handleInteractionStart);
      range?.removeEventListener("blur", handleInteractionEnd);

      el.removeEventListener("loadedmetadata", handleLoadedMetadata);
      el.removeEventListener("timeupdate", handleTimeUpdate);
      el.removeEventListener("durationchange", handleDurationChange);
      el.removeEventListener("canplay", handleCanPlay);
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

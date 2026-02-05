import { usePlayerStore } from "@/stores/player";

interface ExtendedHTMLInputElement extends HTMLInputElement {
  _volumeDirectiveCleanup?: () => void;
}

export default {
  mounted(el: HTMLInputElement) {
    if (el.tagName.toLowerCase() !== "input" || el.type !== "range") {
      console.warn("L'element fourni n'est pas un input range.");
      return;
    }

    const playerStore = usePlayerStore();
    const extendedEl = el as ExtendedHTMLInputElement;

    const hasInteractionMethod = typeof playerStore.setIsVolumeInteraction === "function";

    const handleInteractionStart = () => {
      if (hasInteractionMethod) {
        playerStore.setIsVolumeInteraction(true);
      }
    };

    const handleInteractionEnd = () => {
      if (hasInteractionMethod) {
        playerStore.setIsVolumeInteraction(false);
      }
    };

    el.addEventListener("mousedown", handleInteractionStart);
    el.addEventListener("mouseup", handleInteractionEnd);
    el.addEventListener("touchstart", handleInteractionStart);
    el.addEventListener("touchend", handleInteractionEnd);
    el.addEventListener("focus", handleInteractionStart);
    el.addEventListener("blur", handleInteractionEnd);

    const handleMouseLeave = () => {
      if (playerStore.isPlayerInteraction) {
        handleInteractionEnd();
      }
    };

    el.addEventListener("mouseleave", handleMouseLeave);

    extendedEl._volumeDirectiveCleanup = () => {
      el.removeEventListener("mousedown", handleInteractionStart);
      el.removeEventListener("mouseup", handleInteractionEnd);
      el.removeEventListener("touchstart", handleInteractionStart);
      el.removeEventListener("touchend", handleInteractionEnd);
      el.removeEventListener("focus", handleInteractionStart);
      el.removeEventListener("blur", handleInteractionEnd);
      el.removeEventListener("mouseleave", handleMouseLeave);
    };
  },

  unmounted(el: HTMLInputElement) {
    const extendedEl = el as ExtendedHTMLInputElement;
    if (extendedEl._volumeDirectiveCleanup) {
      extendedEl._volumeDirectiveCleanup();
      delete extendedEl._volumeDirectiveCleanup;
    }
  },
};

import { usePlayerPreparation } from "@/composables/usePlayerPreparation";
import { usePlayerStore } from "@/stores/player";

interface ExtendedHTMLElement extends HTMLElement {
  _rangeDirectiveCleanup?: () => void;
}

export default {
  mounted(el: HTMLElement) {
    if (el.tagName.toLowerCase() !== "input" || (el as HTMLInputElement).type !== "range") {
      console.warn("L'element fourni n'est pas un input de type range.");
      return;
    }

    const playerStore = usePlayerStore();
    const range = el as HTMLInputElement;
    const extendedEl = el as ExtendedHTMLElement;

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

    extendedEl._rangeDirectiveCleanup = () => {
      range?.removeEventListener("mousedown", handleInteractionStart);
      range?.removeEventListener("mouseup", handleInteractionEnd);
      range?.removeEventListener("touchstart", handleInteractionStart);
      range?.removeEventListener("touchend", handleInteractionEnd);
      range?.removeEventListener("focus", handleInteractionStart);
      range?.removeEventListener("blur", handleInteractionEnd);
    };
  },

  unmounted(el: HTMLElement) {
    const extendedEl = el as ExtendedHTMLElement;
    if (extendedEl._rangeDirectiveCleanup) {
      extendedEl._rangeDirectiveCleanup();
      delete extendedEl._rangeDirectiveCleanup;
    }
  },
};

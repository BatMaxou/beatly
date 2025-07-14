import { useRouter } from "vue-router";
import { ApiClient } from "@/stores/api-client/model";
import { useAuthStore } from "@/stores/auth";
import { usePlayerStore } from "@/stores/player";

export function useLogout() {
  const router = useRouter();
  const apiClient = new ApiClient();
  const authStore = useAuthStore();
  const playerStore = usePlayerStore();

  const logout = async () => {
    playerStore.clearQueue();
    apiClient.logout();
    authStore.logout();
    await router.push({ name: "Login" });
  };

  return {
    logout,
  };
}

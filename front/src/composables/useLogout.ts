import { useRouter } from "vue-router";
import { ApiClient } from "@/stores/api-client/model";
import { useAuthStore } from "@/stores/auth";

export function useLogout() {
  const router = useRouter();
  const apiClient = new ApiClient();
  const authStore = useAuthStore();

  const logout = async () => {
    apiClient.logout();
    authStore.logout();
    await router.push({ name: "Login" });
  };

  return {
    logout,
  };
}

import { useRouter } from "vue-router";
import { ApiClient } from "@/stores/api-client/model";

export function useLogout() {
  const router = useRouter();
  const apiClient = new ApiClient();

  const logout = async () => {
    apiClient.logout();
    await router.push({ name: "Root" });
  };

  return {
    logout,
  };
}

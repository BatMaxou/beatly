import type { User } from "@/utils/types";
import { defineStore } from "pinia";
import { useApiClient } from "@/stores/api-client";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: {} as User | null,
  }),
  actions: {
    setUser(user: User | null) {
      this.user = user;
    },
    logout() {
      this.user = null;
    },
    async fetchMe() {
      const { apiClient } = useApiClient();
      try {
        const user = await apiClient.me.get();
        this.user = user;
        return user;
      } catch (error) {
        this.user = null;
        throw error;
      }
    },
  },
});

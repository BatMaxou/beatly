import { Role, type User } from "@/utils/types";
import { defineStore } from "pinia";
import { useApiClient } from "@/stores/api-client";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: {} as User | null,
    isAdmin: false,
  }),
  actions: {
    setUser(user: User | null) {
      this.user = user;
      if (this.user) {
        this.isAdmin = this.user.roles.includes(Role.PLATFORM);
      }
    },
    logout() {
      this.user = null;
    },
    async fetchMe() {
      const { apiClient } = useApiClient();
      try {
        const user = await apiClient.me.get();
        this.user = user;
        if (this.user) {
          this.isAdmin = this.user.roles.includes(Role.PLATFORM);
        }
        return user;
      } catch (error) {
        this.user = null;
        throw error;
      }
    },
  },
});

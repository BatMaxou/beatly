import { defineStore } from "pinia";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: {} as object | null,
  }),
  actions: {
    setUser(user: object | null) {
      this.user = user;
    },
    logout() {
      this.user = {};
    }
  },
});

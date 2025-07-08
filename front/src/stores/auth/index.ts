import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    email: null as string | null,
    loginSuccess: false,
    logoutSuccess: false,
    forgotPasswordMessage: null as string | null,
    user: {},
  }),
  actions: {
    setEmail(email: string | null) {
      this.email = email;
    },
    setLoginSuccess(loginSuccess: boolean) {
      this.loginSuccess = loginSuccess;
    },
    setLogoutSuccess(logoutSuccess: boolean) {
      this.logoutSuccess = logoutSuccess;
    },
    setForgotPasswordMessage(forgotPasswordMessage: string | null) {
      this.forgotPasswordMessage = forgotPasswordMessage;
    },
    logout() {
      this.email = null;
      this.loginSuccess = false;
      this.logoutSuccess = true;
      this.forgotPasswordMessage = null;
    }
  },
});

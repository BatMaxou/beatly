import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    email: "",
    loginSuccess: false,
    logoutSuccess: false,
    forgotPasswordMessage: "",
    user: {},
  }),
  actions: {
    setEmail(email: string | any) {
      this.email = email;
    },
    setLoginSuccess(loginSuccess: boolean) {
      this.loginSuccess = loginSuccess;
    },
    setLogoutSuccess(logoutSuccess: boolean) {
      this.logoutSuccess = logoutSuccess;
    },
    setForgotPasswordMessage(forgotPasswordMessage: string | any) {
      this.forgotPasswordMessage = forgotPasswordMessage;
    },
    setUser(user: object | any) {
      this.user = user;
    },
    logout() {
      this.email = "";
      this.loginSuccess = false;
      this.logoutSuccess = true;
      this.forgotPasswordMessage = "";
      this.user = {};
    }
  },
});

import { defineStore } from "pinia";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    email: "",
    loginSuccess: false,
    forgotPasswordMessage: "",
  }),
  actions: {
    setEmail(email: string | any) {
      this.email = email;
    },
    setLoginSuccess(loginSuccess: boolean) {
      this.loginSuccess = loginSuccess;
    },
    setForgotPasswordMessage(forgotPasswordMessage: string | any) {
      this.forgotPasswordMessage = forgotPasswordMessage;
    },
  },
});

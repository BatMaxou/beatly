<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useHead } from "@unhead/vue";

import { useApiClient } from "@/stores/api-client";
import { useToast } from "@/composables/useToast";
import { useAuthStore } from "@/stores/auth";
import { useUserStore } from "@/stores/user";
import PublicLayout from "@/components/layout/PublicLayout.vue";
import LandingButton from "@/components/buttons/LandingButton.vue";

useHead({
  title: 'Beatly | Se connecter',
});

const router = useRouter();
const { apiClient } = useApiClient();
const { showSuccess, showError } = useToast();
const authStore = useAuthStore();
const userStore = useUserStore();
const email = ref(authStore.email || "");
const isFromLogout = ref(authStore.logoutSuccess || false);
const loading = ref(false);
let emailValue = null;

if (email.value) {
  showSuccess("Compte crée !");
  emailValue = email.value || null;
  authStore.setEmail(null);
}

if (isFromLogout.value) {
  showSuccess("Vous êtes déconnecté !");
  isFromLogout.value = false;
  authStore.setLogoutSuccess(false);
}

if (authStore.forgotPasswordMessage) {
  showSuccess(authStore.forgotPasswordMessage);
  authStore.setForgotPasswordMessage(null);
}

function handleSubmitLoginForm(data: { email: string; password: string }) {
  loading.value = true;
  apiClient.login(data.email, data.password).then((response: { user?: object | null, token?: string }) => {
    if (response.user) {
      authStore.setLoginSuccess(true);
      userStore.setUser({ ...response.user, token: response.token } );
      router.push("/");
    } else {
      loading.value = false;
      showError("Email ou mot de passe incorrect");
    }
  });
}

const handleIconClick = (node: { props: { suffixIcon: string; type: string } }) => {
  node.props.suffixIcon = node.props.suffixIcon === "eye" ? "eyeClosed" : "eye";
  node.props.type = node.props.type === "password" ? "text" : "password";
};

function goToRegister() {
  router.push("/register");
}

function goToForgotPassword() {
  router.push("/forgot-password");
}
</script>

<template>
  <PublicLayout title="Se connecter">
    <FormKit
      type="form"
      :classes="{
        form: 'w-full flex flex-col gap-4 max-w-[400px]',
      }"
      @submit="handleSubmitLoginForm"
      :actions="false"
      :messages-class="'hidden'"
    >
      <div class="flex flex-col gap-2 justify-start">
        <FormKit
          type="email"
          name="email"
          label="Adresse courriel"
          :value="emailValue || ''"
          v-focus="emailValue !== '' ? false : true"
          :classes="{
            input:
              'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
          }"
          validation="required|email"
          validation-visibility="submit"
          placeholder="Adresse email"
        />
      </div>
      <div class="flex flex-col gap-2 justify-start">
        <FormKit
          type="password"
          name="password"
          label="Mot de passe"
          v-focus
          :classes="{
            inner: 'relative flex items-center',
            input:
              'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
            suffixIcon: 'absolute right-4 text-[#5523bf] hover:text-blue-500 cursor-pointer w-6',
          }"
          validation="required"
          validation-visibility="submit"
          placeholder="Mot de passe"
          suffix-icon="eyeClosed"
          @suffix-icon-click="handleIconClick"
          suffix-icon-class="hover:text-blue-500"
        />
        <p
          @click="goToForgotPassword"
          class="text-end text-white font-light underline cursor-pointer"
        >
          Mot de passe oublié ?
        </p>
      </div>

      <div class="flex justify-center mt-8 mb-16">
        <LandingButton
          label="Se connecter"
          type="submit"
          :loading="loading"
        />
      </div>
    </FormKit>

    <p class="text-center mb-4 text-white font-light">Vous n'avez pas de compte ?</p>
    <div class="flex justify-center mb-16">
      <LandingButton label="S'inscrire" @click="goToRegister"/>
    </div>
  </PublicLayout>
</template>

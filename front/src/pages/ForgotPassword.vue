<script setup lang="ts">
import { ref } from "vue";
import arrowLeft from "@/assets/icons/arrow-left-light.svg";
import { useRouter } from "vue-router";
import { useApiClient } from "@/stores/api-client";
import PublicLayout from "@/components/PublicLayout.vue";
import { useToast } from "@/composables/useToast";
import { useAuthStore } from "@/stores/auth";
import LandingButton from "@/components/buttons/LandingButton.vue";

const router = useRouter();
const { apiClient } = useApiClient();
const { showError } = useToast();
const authStore = useAuthStore();
const loading = ref(false);

function goToLogin() {
  router.push("/login");
}

function handleSubmitForgotPasswordForm(data) {
  loading.value = true;
  apiClient.user
    .forgotPassword({ email: data.email })
    .then((response) => {
      if (response.result) {
        authStore.setForgotPasswordMessage(
          "Un email de réinitialisation vous sera envoyé dans quelques instants si l'adresse existe.",
        );
        router.push("/login");
      } else {
        loading.value = false;
        showError("Une erreur est survenue. Veuillez réessayer plus tard.");
      }
    })
    .catch(() => {
      loading.value = false;
      showError("Une erreur est survenue. Veuillez réessayer plus tard.");
    });
}
</script>

<template>
  <PublicLayout title="Mot de passe oublié">
    <div class="flex justify-start items-center max-w-[400px] w-full mb-8">
      <button
        class="uppercase border border-white rounded-3xl w-fit ps-5 pe-8 py-2 text-sm font-bold text-white hover:bg-[#B00D70] hover:border-[#B00D70] hover:text-white transition"
        @click="goToLogin"
      >
        <img :src="arrowLeft" class="w-6 h-6 inline-block mr-2" alt="Retour" />
        Retour
      </button>
    </div>

    <FormKit
      type="form"
      :classes="{
        form: 'w-full flex flex-col gap-4 max-w-[400px]',
      }"
      @submit="handleSubmitForgotPasswordForm"
      :actions="false"
      :messages-class="'hidden'"
    >
      <div class="flex flex-col gap-2 justify-start">
        <FormKit
          type="email"
          name="email"
          label="Adresse courriel"
          :classes="{
            input:
              'px-4 py-2 max-h-12 h-12 w-full text-black rounded border border-[#5523bf] focus:outline-none focus:ring-2 focus:ring-[#5523bf]',
          }"
          validation="required|email"
          validation-visibility="submit"
          placeholder="Adresse email"
        />
      </div>
      <div class="flex justify-center mt-8 mb-16">
        <LandingButton
          label="Réinitialisation du mot de passe"
          type="submit"
          :loading="loading"
        />

      </div>
    </FormKit>
  </PublicLayout>
</template>

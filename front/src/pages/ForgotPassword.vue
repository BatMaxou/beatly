<script setup lang="ts">
import imageVinyl from "@/assets/images/vinyl-black-and-white-landing.jpg";
import logo from "@/assets/beatly-logo-white.png";
import { useRouter } from "vue-router";
import { useApiClient } from "@/stores/api-client";
import { ref } from "vue";

const router = useRouter();
const { apiClient } = useApiClient();
const successMessage = ref("");
const errorMessage = ref("");
const isSubmitting = ref(false);

function handleSubmitResetPasswordForm(data) {
  isSubmitting.value = true;
  errorMessage.value = "";
  successMessage.value = "";

  apiClient.user
    .forgotPassword({ email: data.email })
    .then((response) => {
      isSubmitting.value = false;
      if (response.result) {
        successMessage.value =
          "Un email de réinitialisation vous sera envoyé dans quelques instants si l'adresse existe.";
        setTimeout(() => {
          router.push("/login");
        }, 5000);
      } else {
        errorMessage.value = "Une erreur est survenue. Veuillez réessayer plus tard.";
      }
    })
    .catch(() => {
      isSubmitting.value = false;
      errorMessage.value = "Une erreur est survenue. Veuillez réessayer plus tard.";
    });
}
</script>

<template>
  <div class="relative min-h-screen flex justify-center">
    <div
      class="absolute inset-0 bg-cover bg-center z-0"
      :style="{ backgroundImage: `url(${imageVinyl})` }"
    ></div>
    <div
      class="absolute inset-0 z-10"
      style="background: linear-gradient(to bottom, #5523bf, #b00d72); opacity: 0.7"
    ></div>
    <div class="relative container z-20 flex flex-col items-center mt-12 w-full">
      <img :src="logo" alt="Logo Beatly" class="h-24 mb-4" />

      <h2 class="text-2xl uppercase text-center mt-12 mb-20">Mot de passe oublié</h2>

      <div
        v-if="successMessage"
        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-8 max-w-[400px] w-full"
      >
        {{ successMessage }}
      </div>

      <div
        v-if="errorMessage"
        class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-8 max-w-[400px] w-full"
      >
        {{ errorMessage }}
      </div>

      <FormKit
        type="form"
        :classes="{
          form: 'w-full flex flex-col gap-4 max-w-[400px]',
        }"
        @submit="handleSubmitResetPasswordForm"
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
          <FormKit
            type="submit"
            label="Réinitialisation du mot de passe"
            :disabled="isSubmitting"
            :classes="{
              input:
                'uppercase bg-[#B00D70] rounded-3xl w-fit px-10 py-2 text-sm font-bold text-white hover:bg-[#940a5e] transition disabled:opacity-50',
            }"
          />
        </div>
      </FormKit>
    </div>
  </div>
</template>

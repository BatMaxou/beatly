<script setup lang="ts">
import imageVinyl from "@/assets/images/vinyl-black-and-white-landing.jpg";
import logo from "@/assets/beatly-logo-white.png";
import { defineProps } from "vue";

defineProps({
  title: {
    type: String,
  },
});
</script>

<template>
  <div class="relative min-h-screen flex justify-center text-white">
    <div
      class="absolute inset-0 bg-cover bg-center z-0"
      :style="{ backgroundImage: `url(${imageVinyl})` }"
    ></div>

    <div
      class="absolute inset-0 z-10"
      style="background: linear-gradient(to bottom, #5523bf, #b00d72); opacity: 0.7"
    ></div>

    <div class="relative container z-20 flex flex-col items-center mt-12 w-full p-4">
      <img :src="logo" alt="Logo Beatly" class="h-24 mb-4" />

      <h2 v-if="title" class="text-2xl uppercase text-center mt-12 mb-20">{{ title }}</h2>

      <slot></slot>
    </div>
  </div>
</template>

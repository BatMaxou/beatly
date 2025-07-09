import "./assets/main.css";
import "./assets/main-build.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import { plugin as FormKitPlugin, defaultConfig } from "@formkit/vue";
import { fr } from "@formkit/i18n";
import router from "./router";
import Vue3Toastify, { type ToastContainerOptions } from "vue3-toastify";
import AudioDirective from "./directives/Audio";
import VolumeRangeDirective from "./directives/VolumeRange";

import App from "./App.vue";

createApp(App)
  .use(createPinia())
  .use(router)
  .use(Vue3Toastify, {
    expandCustomProps: true,
  } as ToastContainerOptions)
  .use(
    FormKitPlugin,
    defaultConfig({
      locales: { fr },
      locale: "fr",
    }),
  ).directive("audio", AudioDirective)
  .directive("volume-range", VolumeRangeDirective)
  .mount("#app");

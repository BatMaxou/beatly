import "./assets/main.css";
import "./assets/main-build.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import { plugin as FormKitPlugin, defaultConfig } from "@formkit/vue";
import { fr } from "@formkit/i18n";
import Vue3Toastify, { type ToastContainerOptions } from "vue3-toastify";
import VueMatomo from 'vue-matomo'

import router from "./router";
import AudioDirective from "./directives/Audio";
import RangeDirective from "./directives/CurrentTimeRange";
import VolumeRangeDirective from "./directives/VolumeRange";

import App from "./App.vue";
import { apiBaseUrl, matomoSiteId, matomoUrl, ressourceUrl } from "./utils/tools";

createApp(App)
  .use(createPinia())
  .use(router)
  .use(Vue3Toastify, {
    expandCustomProps: true,
  } as ToastContainerOptions)
  .use(VueMatomo, {
    host: matomoUrl,
    siteId: matomoSiteId,
    router: router,
  })
  .use(
    FormKitPlugin,
    defaultConfig({
      locales: { fr },
      locale: "fr",
    }),
  )
  .directive("audio", AudioDirective)
  .directive("current-time-range", RangeDirective)
  .directive("volume-range", VolumeRangeDirective)
  .mount("#app");

window._paq = window._paq || [];
window._paq.push(['trackPageView']);

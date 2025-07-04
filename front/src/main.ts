import "./assets/main.css";
import "./assets/main-build.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import { plugin as FormKitPlugin, defaultConfig } from "@formkit/vue";
import { fr } from "@formkit/i18n";
import router from "./router";

import App from "./App.vue";

createApp(App)
  .use(createPinia())
  .use(router)
  .use(
    FormKitPlugin,
    defaultConfig({
      locales: { fr },
      locale: "fr",
    }),
  )
  .mount("#app");

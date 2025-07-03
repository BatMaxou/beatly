import "./assets/main.css";
import "./assets/main-build.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import { plugin, defaultConfig } from "@formkit/vue";
import router from "./router";

import App from "./App.vue";

createApp(App).use(createPinia()).use(router).use(plugin, defaultConfig).mount("#app");

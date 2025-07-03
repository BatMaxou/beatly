import { createRouter, createWebHistory } from "vue-router";
import Home from "../pages/Home.vue";
import MusicFile from "../pages/MusicFile.vue";
import Ui from "../pages/Ui.vue";
import Auth from "../pages/Auth.vue";
import Register from "../pages/Register.vue";
import Login from "../pages/Login.vue";
import NotFound from "../pages/NotFound.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: Home,
  },
  {
    path: "/test-music-file",
    name: "MusicFile",
    component: MusicFile,
  },
  {
    path: "/ui",
    name: "Ui",
    component: Ui,
  },
  {
    path: "/auth",
    name: "Auth",
    component: Auth,
  },
  {
    path: "/register",
    name: "Register",
    component: Register,
  },
  {
    path: "/login",
    name: "Login",
    component: Login,
  },
  {
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;

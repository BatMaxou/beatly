import { createRouter, createWebHistory } from "vue-router";
import Home from "../pages/Home.vue";
import MusicFile from "../pages/MusicFile.vue";
import Ui from "../pages/Ui.vue";
import Landing from "../pages/Landing.vue";
import Register from "../pages/Register.vue";
import Login from "../pages/Login.vue";
import NotFound from "../pages/NotFound.vue";

const token = localStorage.getItem("token");

const routes = [
  {
    path: "/",
    name: "Home",
    component: token ? Home : Landing,
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

import { createRouter, createWebHistory } from "vue-router";
import type { RouteRecordRaw } from "vue-router";
import Home from "../pages/Home.vue";
import MusicFile from "../pages/MusicFile.vue";
import Ui from "../pages/Ui.vue";
import RootPage from "../pages/RootPage.vue";
import Register from "../pages/Register.vue";
import Login from "../pages/Login.vue";
import ForgotPassword from "../pages/ForgotPassword.vue";
import ResetPassword from "../pages/ResetPassword.vue";
import NotFound from "../pages/NotFound.vue";
import { getCookie } from "@/utils/cookies";
import { useLogout } from "@/composables/useLogout";

const routes: RouteRecordRaw[] = [
  {
    path: "/",
    component: RootPage,
    name: "Root",
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
  {
    path: "/logout",
    name: "Logout",
    component: NotFound,
    beforeEnter: async (to, from, next) => {
      const { logout } = useLogout();
      await logout();
      next({ name: "Root" });
    },
  },
  {
    path: "/forgot-password",
    name: "ForgotPassword",
    component: ForgotPassword,
  },
  {
    path: "/reset-password/:token",
    name: "ResetPassword",
    component: ResetPassword,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Vérification en amont pour protéger les routes sécurisées
router.beforeEach((to, from, next) => {
  // Vérifier si la page nécessite une authentification
  const publicPages = ["Root", "Login", "Register", "ForgotPassword", "ResetPassword", "NotFound"];
  const authRequired = !publicPages.includes(to.name as string);
  const token = getCookie("token");

  // Si l'utilisateur n'est pas connecté et essaie d'accéder à une page protégée
  if (authRequired && !token) {
    return next({ name: "Login" });
  }

  next();
});

export default router;

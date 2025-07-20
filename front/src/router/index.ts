import { createRouter, createWebHistory } from "vue-router";
import type { RouteRecordRaw } from "vue-router";
import Ui from "../pages/UiPage.vue";
import RootPage from "../pages/RootPage.vue";
import Register from "../pages/RegisterPage.vue";
import Login from "../pages/LoginPage.vue";
import ForgotPassword from "../pages/ForgotPasswordPage.vue";
import ResetPassword from "../pages/ResetPasswordPage.vue";
import PlaylistPage from "../pages/PlaylistPage.vue";
import PlaylistFavoritePage from "../pages/PlaylistFavoritePage.vue";
import AlbumPage from "../pages/AlbumPage.vue";
import ArtistPage from "../pages/ArtistPage.vue";
import LibraryPage from "../pages/LibraryPage.vue";
import AccountPage from "../pages/AccountPage.vue";
import NotFound from "../pages/NotFoundPage.vue";
import { getCookie } from "@/utils/cookies";
import { useLogout } from "@/composables/useLogout";
import { useUserStore } from "@/stores/user";
import { Role } from "@/utils/types";

const routes: RouteRecordRaw[] = [
  {
    path: "/",
    component: RootPage,
    name: "Root",
  },
  {
    path: "/playlist/favoris",
    component: PlaylistFavoritePage,
    name: "PlaylistFavorite",
  },
  {
    path: "/playlist/:id",
    name: "Playlist",
    component: PlaylistPage,
  },
  {
    path: "/album/:id",
    name: "Album",
    component: AlbumPage,
  },
  {
    path: "/artiste/:id",
    name: "Artist",
    component: ArtistPage,
  },
  {
    path: "/bibliotheque",
    name: "Library",
    component: LibraryPage,
  },
  {
    path: "/mon-compte",
    name: "Account",
    component: AccountPage,
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
router.beforeEach(async (to, from, next) => {
  // Vérifier si la page nécessite une authentification
  const publicPages = ["Root", "Login", "Register", "ForgotPassword", "ResetPassword", "NotFound"];
  const authRequired = !publicPages.includes(to.name as string);
  const token = getCookie("token");

  // Si l'utilisateur n'est pas connecté et essaie d'accéder à une page protégée
  if (authRequired && !token) {
    return next({ name: "Login" });
  }

  if (authRequired || (from.path === "/" && token)) {
    const userStore = useUserStore();
    if (!userStore.user || !userStore.user.id) {
      try {
        await userStore.fetchMe();
      } catch {
        return next({ name: "Root" });
      }
    }
  }

  if (to.path.startsWith("/admin")) {
    const userStore = useUserStore();
    const userRoles = userStore.user?.roles || [];

    if (!userRoles.includes(Role.PLATFORM)) {
      return next({ name: "Root" });
    }
  }

  next();
});

export default router;

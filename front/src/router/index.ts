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
import AdminPage from "../pages/admin/AdminPage.vue";
import AdminRequestsPage from "../pages/admin/AdminRequestsPage.vue";
import AdminArtistsPage from "../pages/admin/AdminArtistsPage.vue";
import AdminAlbumsPage from "../pages/admin/AdminAlbumsPage.vue";
import AdminMusicsPage from "../pages/admin/AdminMusicsPage.vue";
import AdminPlaylistsPage from "../pages/admin/AdminPlaylistsPage.vue";
import AdminUsersPage from "../pages/admin/AdminUsersPage.vue";
import AdminArtistDetailPage from "../pages/admin/AdminArtistDetailPage.vue";
// import AlbumDetailPage from "../pages/artist/AlbumDetailPage.vue";
import ArtistDashboardPage from "../pages/artist/ArtistDashboardPage.vue";
import ArtistAlbumListPage from "../pages/artist/ArtistAlbumListPage.vue";
import ArtistMusicListPage from "../pages/artist/ArtistMusicListPage.vue";
import NotFound from "../pages/NotFoundPage.vue";
import { getCookie } from "@/utils/cookies";
import { useLogout } from "@/composables/useLogout";
import { useUserStore } from "@/stores/user";
import { Role } from "@/utils/types";
import AdminAlbumDetailPage from "@/pages/admin/AdminAlbumDetailPage.vue";
import AdminPlaylistDetailPage from "@/pages/admin/AdminPlaylistDetailPage.vue";
import ArtistAlbumDetailPage from "@/pages/artist/ArtistAlbumDetailPage.vue";

const routes: RouteRecordRaw[] = [
  {
    path: "/",
    component: RootPage,
    name: "Root",
    meta: {
      title: "Accueil",
      description: "Page d'accueil",
      ogTitle: "Mon site - Accueil",
    },
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
  // Pages d'administration
  {
    path: "/admin",
    name: "Admin",
    component: AdminPage,
  },
  {
    path: "/admin/artistes",
    name: "AdminArtists",
    component: AdminArtistsPage,
  },
  {
    path: "/admin/artistes/:id",
    name: "AdminArtistDetail",
    component: AdminArtistDetailPage,
  },
  {
    path: "/admin/albums",
    name: "AdminAlbums",
    component: AdminAlbumsPage,
  },
  {
    path: "/admin/albums/:id",
    name: "AdminAlbumsDetail",
    component: AdminAlbumDetailPage,
  },
  {
    path: "/admin/musiques",
    name: "AdminMusics",
    component: AdminMusicsPage,
  },
  {
    path: "/admin/playlists",
    name: "AdminPlaylists",
    component: AdminPlaylistsPage,
  },
  {
    path: "/admin/playlists/:id",
    name: "AdminPlaylistDetail",
    component: AdminPlaylistDetailPage,
  },
  {
    path: "/admin/utilisateurs",
    name: "AdminUsers",
    component: AdminUsersPage,
  },
  {
    path: "/admin/demandes",
    name: "AdminRequests",
    component: AdminRequestsPage,
  },

  // Pages d'artistes
  {
    path: "/artist/",
    name: "ArtistDashboard",
    component: ArtistDashboardPage,
  },
  {
    path: "/artist/albums",
    name: "ArtistAlbums",
    component: ArtistAlbumListPage,
  },
  {
    path: "/artist/musiques",
    name: "ArtistMusics",
    component: ArtistMusicListPage,
  },
  {
    path: "/artist/album/:id",
    name: "ArtistAlbumDetail",
    component: ArtistAlbumDetailPage,
  },

  // Pages publiques
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

  if (authRequired && token) {
    const userStore = useUserStore();
    if (!userStore.user || !userStore.user.id) {
      try {
        const user = await userStore.fetchMe();
        if (!user || !user.id) {
          const { logout } = useLogout();
          await logout();
          return next({ name: "Login" });
        }
      } catch (error) {
        console.error("Erreur lors de la récupération de l'utilisateur:", error);
        const { logout } = useLogout();
        await logout();
        return next({ name: "Login" });
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

  if (to.path.startsWith("/artist") && !to.path.startsWith("/artiste")) {
    const userStore = useUserStore();
    const userRoles = userStore.user?.roles || [];

    if (!userRoles.includes(Role.ARTIST)) {
      return next({ name: "Root" });
    }
  }

  next();
});

export default router;

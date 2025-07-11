export enum Role {
  PLATFORM = "ROLE_PLATFORM",
  ARTIST = "ROLE_ARTIST",
  USER = "ROLE_USER",
  BANNED = "ROLE_BANNED",
}

export enum PlaylistType {
  PRIVATE = "Playlist",
  PLATFORM = "PlatformPlaylist",
}

export interface User {
  name: string;
  email: string;
  roles: Role[];
  avatar?: string;
  wallpaper?: string;
};

export interface Artist extends User {
  musics?: Music[];
  albums?: Album[];
};

export type AlbumMusic = {
  id: number;
  music: Music|string;
  position: number;
  addedAt: string;
};

export type Album = {
  id: number;
  title: string;
  releaseDate: string;
  musics: AlbumMusic[];
  cover?: string;
  wallpaper?: string;
};

export type Category = {
  id: number;
  name: string;
  color: string;
};

export type MusicFile = {
  id: number;
  contentUrl: string;
};

export type Music = {
  id: number;
  title: string;
  artists: Artist[];
  categories: Category[];
  file?: string;
  cover?: string;
  listeningsNumber?: number;
};

export type PlaylistMusic = {
  id: number;
  music: Music|string;
  addedAt: string;
};

export type Playlist = {
  id: number;
  title: string;
  musics: PlaylistMusic[];
  cover?: string;
  wallpaper?: string;
  '@type'?: PlaylistType;
};

export type Listen = {
  music: string,
  playlist?: string,
  album?: string,
}

export type LastListened = {
  target: Music|Playlist|Album,
  listenedAt: string,
}

export type Dashboard = {
  lastListened: LastListened[];
  mostPopularCategories: Category[];
  mostLikedMusics: Music[];
  mostListenedMusics: Music[];
}

export type Recommendation = {
  recommendations: Music[];
}

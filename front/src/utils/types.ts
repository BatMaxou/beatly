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
  id: number;
  name: string;
  email: string;
  roles: Role[];
  avatar?: string;
  wallpaper?: string;
}

export interface Artist extends User {
  musics?: Music[];
  featuredMusics?: Music[];
  albums?: Album[];
}

export type AlbumMusic = {
  id: number;
  music: Music | string;
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
  position?: number;
};

export type Music = {
  id: number;
  title: string;
  mainArtist: Artist;
  artists: Artist[];
  categories: Category[];
  file?: string;
  cover?: string;
  listeningsNumber?: number;
};

export type PlaylistMusic = {
  id: number;
  music: Music;
  addedAt: string | null;
};

export type Playlist = {
  id: number;
  title: string;
  musics: PlaylistMusic[];
  cover?: string;
  wallpaper?: string;
  "@type"?: PlaylistType;
};

export type Listen = {
  music: string;
  playlist?: string;
  album?: string;
};

export type LastListened = {
  target: Music | Playlist | Album;
  listenedAt: string;
};

export type Dashboard = {
  lastListened: LastListened[];
  mostPopularCategories: Category[];
  mostLikedMusics: Music[];
  mostListenedMusics: Music[];
};

export type Recommendation = {
  recommendations: Music[];
};

export type QueueItem = {
  music: Music;
  position: number;
};

export type Queue = {
  queueItems: QueueItem[];
};

export type AddToQueue = {
  music?: string;
  playlist?: string;
  album?: string;
  shouldBeNext?: boolean;
  currentPosition?: number;
};

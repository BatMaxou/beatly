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

export type User = {
  name: string;
  email: string;
  roles: Role[];
};

export type Artist = {
  id: number;
  name: string;
};

export type Album = {
  id: number;
  title: string;
  releaseDate: string;
  musics: Music[];
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
  file?: string | null;
  cover?: string | null;
  listeningsNumber?: number;
};

export type PlaylistMusic = {
  id: number;
  music: Music|string;
  position: number;
  addedAt: string;
};

export type Playlist = {
  id: number;
  title: string;
  musics: PlaylistMusic[];
  '@type'?: PlaylistType;
};

export type Listen = {
  music: string,
  playlist?: string,
  album?: string,
}

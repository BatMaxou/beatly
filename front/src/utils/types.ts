
export enum Role {
  PLATFORM = 'ROLE_PLATFORM',
  ARTIST = 'ROLE_ARTIST',
  USER = 'ROLE_USER',
  BANNED = 'ROLE_BANNED',
}

export type User = {
  name: string;
  email: string;
  roles: Role[];
};

export type Artist = {
  id: number;
  name: string;
}

export type Album = {
  id: number;
  title: string;
}

export type Category = {
  id: number;
  name: string;
}

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
}

<?php

namespace App\Enum;

enum ApiReusableRoute: string
{
    case ME = 'api_me';
    case SEARCH = 'api_search';

    case CREATE_ALBUM = 'api_create_album';
    case CREATE_MUSIC = 'api_create_music';
    case CREATE_PLAYLIST = 'api_create_playlist';

    case UPDATE_MUSIC = 'api_update_music';
    case UPDATE_ALBUM_FILES = 'api_update_album_files';
    case UPDATE_MUSIC_FILES = 'api_update_music_files';
    case UPDATE_PLAYLIST_FILES = 'api_update_playlist_files';
    case UPDATE_USER_FILES = 'api_update_user_files';

    case GET_DASHBOARD = 'api_get_dashboard';
    case GET_RECOMMENDATIONS = 'api_get_recommendations';
    case GET_QUEUE = 'api_get_queue';
    case GET_FAVORITES = 'api_get_favorites';
    case GET_FAVORITE_MUSICS = 'api_get_favorite_musics';
    case GET_FAVORITE_ALBUMS = 'api_get_favorite_albums';
    case GET_FAVORITE_PLAYLISTS = 'api_get_favorite_playlists';

    case GET_COLLECTION_LAST_LISTENED = 'api_get_collection_last_listened';
}

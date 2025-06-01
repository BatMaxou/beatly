<script setup>
  import AlbumMusicList from '@/components/lists/AlbumMusicList.vue'
  import AlbumPlayable from '@/components/albums/AlbumPlayable.vue'
  import ArtistProfile from '@/components/artists/ArtistProfile.vue'
  import cover1 from '@/assets/music/cover1.jpg'
  import artist1 from '@/assets/music/artist1.jpeg'

  const albumMusicList = [
    {
      id: 1,
      title: 'Pain',
      duration: '142',
      position: 1
    },
    {
      id: 2,
      title: 'Hardware',
      duration: '153',
      position: 2
    },
    {
      id: 3,
      title: 'Processor',
      duration: '130',
      position: 3
    },
    {
      id: 4,
      title: 'Digital Dreams',
      duration: '175',
      position: 4
    },
    {
      id: 5,
      title: 'Neon Nights',
      duration: '168',
      position: 5
    },
    {
      id: 6,
      title: 'Binary Beat',
      duration: '147',
      position: 6
    },
    {
      id: 7,
      title: 'Silicon Soul',
      duration: '195',
      position: 7
    },
    {
      id: 8,
      title: 'Electric Echo',
      duration: '162',
      position: 8
    },
    {
      id: 9,
      title: 'Memory Lane',
      duration: '183',
      position: 9
    },
    {
      id: 10,
      title: 'Quantum Quirk',
      duration: '159',
      position: 10
    }
  ]
    
  const customListStyles = {
    musicList: {
      maxWidth: '650px',
      marginTop: "2rem",
      borderRadius: '8px',
      overflow: 'hidden'
    },
    musicElement: {
      borderBottom: '1px solid rgba(200, 200, 200, 0.2)',
      transition: 'background 0.2s ease'
    },
    musicHeader: {
      display: 'flex',
      alignItems: 'center'
    },
    positionContainer: {
      minWidth: '2rem',
      display: 'flex',
      justifyContent: 'center'
    },
    positionNumber: {
      color: '#888',
      fontWeight: '500'
    },
    dividerSpan: {
      color: '#aaa',
      margin: '0 8px'
    },
    titleSpan: {
      fontWeight: '500'
    },
    musicDuration: {
      color: '#888',
      fontWeight: '400'
    },
    durationText: {
      margin: 0,
      fontSize: '0.9rem'
    }
  }
</script>

<template>
    <h2>Cover</h2>
    <div class="ui-music-showcase">
    
        <div class="form-section">
            <h3>Pochette d'album</h3>
            <AlbumPlayable 
                :albumCover="cover1"
                albumName="Pain"
                artistName="Ryan Jones"
                releaseYear="2022"
            />
        </div>

        <!-- Rendre cliquable -->
        <div class="form-section">
            <h3>Pochette d'artiste</h3>
            <ArtistProfile 
                :artistCover="artist1"
                artistName="Ryan Jones"
            />
        </div>
    </div>

    <h2>Musique</h2>
    <div class="ui-music-list">
        <div class="musics-section">
            <h3>Liste ( Album )</h3>
            <AlbumMusicList 
                :musicList="albumMusicList"
                :customStyles="customListStyles"
            />
        </div>
      </div>
</template>

<style scoped>
.ui-music-showcase {
  display: flex; 
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: flex-start;
  gap: 5rem;
  padding: 1rem;
  margin-bottom: 2rem;
}
</style>
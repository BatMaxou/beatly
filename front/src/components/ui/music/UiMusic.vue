<script setup>
    import PlayPauseButton from '@/components/ui/buttons/PlayPauseButton.vue'
</script>

<template>
    <div class="ui-albums-showcase">
        <h2>Musique</h2>
    
        <!-- A externaliser en composant -->
        <div class="form-section">
            <h3>Pochette d'album</h3>
            <div class="album-row">
                <div class="album-container">
                    <img src="@/assets/music/cover1.jpg" alt="Album Name" class="album-cover">
                    <div class="album-overlay">
                        <PlayPauseButton />
                    </div>
                  </div>
                  <div class="album-informations">
                      <h4>Pain</h4>
                      <p>Ryan Jones &middot; 2022</p>
                  </div>
            </div>
        </div>

        <!-- A externaliser en composant -->
        <!-- Rendre cliquable -->
        <div class="form-section">
            <h3>Pochette d'artiste</h3>
            <div class="artist-row">
                <div class="artist-container">
                    <img src="@/assets/music/artist1.jpeg" alt="artist Name" class="artist-cover">
                  </div>
                  <div class="artist-informations">
                      <h4>Ryan Jones</h4>
                  </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.ui-albums-showcase {
  padding: 1rem;
}

.album-section {
  margin-bottom: 2rem;
}

.album-row, .artist-row {
  display: flex;
  flex-wrap: wrap;
  gap: .5rem;
  margin: 1rem 0;
  max-width: 160px;
}

.album-container, .artist-container {
  position: relative;
  width: 100%;
  overflow: hidden;
}

.album-cover, .artist-cover {
  width: 100%;
  display: block;
  transition: transform 0.3s ease;
}

.album-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.play-icon {
  width: 30px;
  height: 30px;
  padding: 4rem;
  box-sizing: content-box;
}

.album-container:hover .album-overlay {
  opacity: 1;
}
</style>
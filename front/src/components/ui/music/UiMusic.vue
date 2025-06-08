<script setup>
import { ref } from "vue";
import MusicList from "@/components/lists/MusicList.vue";
import AlbumPlayable from "@/components/cards/AlbumPlayableCard.vue";
import ArtistProfile from "@/components/artists/ArtistProfile.vue";
import MusicStyleCard from "@/components/musicstyles/MusicStyleCard.vue";
import cover1 from "@/assets/music/cover1.jpg";
import artist1 from "@/assets/music/artist1.jpeg";

const musicStyle = ref("electronic");
const musicStyles = ["rock", "pop", "jazz", "electronic", "hiphop", "classical", "reggae", "rnb"];

// Mapping des noms de genres en français
const genreNames = {
  rock: "Rock",
  pop: "Pop",
  jazz: "Jazz",
  electronic: "Électronique",
  hiphop: "Hip-Hop",
  classical: "Classique",
  reggae: "Reggae",
  rnb: "R&B",
};

const albumMusicList = [
  {
    id: 1,
    title: "Pain",
    duration: "142",
    position: 1,
    cover: cover1,
  },
  {
    id: 2,
    title: "Hardware",
    duration: "153",
    position: 2,
    cover: cover1,
  },
  {
    id: 3,
    title: "Processor",
    duration: "130",
    position: 3,
    cover: cover1,
  },
  {
    id: 4,
    title: "Digital Dreams",
    duration: "175",
    position: 4,
    cover: cover1,
  },
  {
    id: 5,
    title: "Neon Nights",
    duration: "168",
    position: 5,
    cover: cover1,
  },
  {
    id: 6,
    title: "Binary Beat",
    duration: "147",
    position: 6,
    cover: cover1,
  },
  {
    id: 7,
    title: "Silicon Soul",
    duration: "195",
    position: 7,
    cover: cover1,
  },
  {
    id: 8,
    title: "Electric Echo",
    duration: "162",
    position: 8,
    cover: cover1,
  },
  {
    id: 9,
    title: "Memory Lane",
    duration: "183",
    position: 9,
    cover: cover1,
  },
  {
    id: 10,
    title: "Quantum Quirk",
    duration: "159",
    position: 10,
    cover: cover1,
  },
];
</script>

<template>
  <div class="p-4">
    <div class="flex flex-row gap-8">
      <div class="flex-1">
        <h2 class="text-xl font-bold mb-4">Cover</h2>
        <div class="flex flex-row flex-wrap justify-start gap-20 p-4 mb-8">
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Pochette d'album</h3>
            <AlbumPlayable
              :albumCover="cover1"
              albumName="Pain"
              artistName="Ryan Jones"
              releaseYear="2022"
            />
          </div>

          <!-- Rendre cliquable -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Pochette d'artiste</h3>
            <ArtistProfile :artistCover="artist1" artistName="Ryan Jones" class="cursor-pointer" />
          </div>
        </div>
      </div>
      <div class="flex-1">
        <h2 class="text-xl font-bold mb-4">Genre</h2>
        <div class="flex flex-col gap-8 w-[300px]">
          <div class="flex flex-col gap-2 mb-4">
            <label for="music-style-select">Sélectionner un genre :</label>
            <select
              id="music-style-select"
              v-model="musicStyle"
              class="p-2 rounded border border-gray-300 bg-white text-base w-full max-w-[300px]"
            >
              <option v-for="style in musicStyles" :key="style" :value="style">
                {{ genreNames[style] }}
              </option>
            </select>
          </div>
          <MusicStyleCard :style="musicStyle" :label="genreNames[musicStyle]" />
        </div>
      </div>
    </div>
    <h2 class="text-xl font-bold mb-4">Musique</h2>
    <div class="max-w-[650px] mt-8 overflow-hidden">
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-2">Liste ( Album )</h3>
        <MusicList :musicList="albumMusicList" :origin="`playlist`" :theme="`light`" />
      </div>
    </div>
  </div>
</template>

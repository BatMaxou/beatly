<script setup lang="ts">
  import { ref, watch } from 'vue';

  import AudioPlayer from '@/components/ui/player/AudioPlayer.vue';
  import { useApiClient } from '@/stores/api-client';
  import { streamToAudioUrl } from '@/utils/stream';

  const { apiClient } = useApiClient();

  const inputNew = ref<File | null>(null);
  const inputUpdate = ref<File | null>(null);
  const musicId = ref<number | any>(null);
  const musicUrl = ref<string>('');

  // LOGIN TO GET TOKEN IF NOT EXIST INTO COOKIES
  apiClient.login('beatly@gmail.com', 'azerty')
    .then(response => console.log('Login response:', response));

  // GET INFOS OF ACCOUNT
  apiClient.me.get()
    .then(response => console.log('User info:', response))

  // CREATE NEW FILE ON INPUT
  watch(() => inputNew.value, async () => {
    if (!inputNew.value) {
      return;
    }

    apiClient.music.upload(inputNew.value)
      .then(response => {
        console.log('Upload response:', response);
        musicId.value = response.id;
      })
  });

  // UPDATE EXISTING FILE FOR NEW ONE ON INPUT
  watch(() => inputUpdate.value, async () => {
    if (!inputUpdate.value) {
      return;
    }

    apiClient.music.updateFile(musicId.value, inputUpdate.value)
      .then(response => {
        console.log('Upload response:', response);
        musicId.value = response.id
      })
  });

  // FILL PLAYER WITH THE MUSIC OF THE INPUT (NEW UPLOAD OR UPDATE)
  watch(() => musicId.value, async () => {
    if (!musicId.value) {
      return;
    }

    apiClient.music.getFile(musicId.value)
      .then(async (response) => {
        musicUrl.value = await streamToAudioUrl(response);
      })
  });

  const onInputNew = (event: Event) => {
    const target = event.target as HTMLInputElement;
    inputNew.value = target.files?.[0] || null;
  };

  const onInputUpdate = (event: Event) => {
    const target = event.target as HTMLInputElement;
    inputUpdate.value = target.files?.[0] || null;
  };

  const onDeleteClick = () => {
    apiClient.music.deleteFile(musicId.value)
      .then(response => {
        if (response.success) {
          musicId.value = null;
          musicUrl.value = '';
        }
      })
  }
</script>

<template>
    <h1>Home</h1>

    <div>
      <h2>Create File</h2>
      <input type="file" @change="onInputNew" />
    </div>

    <div v-if="musicId">
      <h2>Update File</h2>
      <input type="file" @change="onInputUpdate" />
    </div>

    <AudioPlayer v-if="musicUrl.startsWith('blob:')" :url="musicUrl" />

    <button v-if="musicId" @click="onDeleteClick">Delete File</button>
</template>

<script lang="ts" setup>
defineProps({
  label: {
    type: String,
    required: true
  },
  icon: {
    type: String,
    required: true
  }
})
</script>

<template>
  <!-- Un élément de nav qui change au hover, au clic,  -->
   <div class="bg-[#2E0B40]/30 hover:bg-[#2E0B40]/80 transition duration-500 flex justify-start items-center cursor-pointer py-5 ps-4">
      <img :src="icon" alt="Icon" class="inline-block w-6 h-6 mr-2" />
      <span class="text-white text-xl hidden sm:block">{{ label }}</span>
   </div>
</template>

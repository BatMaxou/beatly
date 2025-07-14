<script lang="ts" setup>
import { ref } from "vue";
import search from "@/assets/icons/search-light.svg";

const searchInput = ref<HTMLInputElement | null>(null)
const searchDiv = ref<HTMLInputElement | null>(null)

function focusSearchInput() {
  searchInput.value?.focus()
  searchDiv.value?.focus()
}
</script>

<template>
  <div
    class="search bg-[#2E0B40]/30 hover:bg-[#2E0B40]/80 focus-within:bg-[#2E0B40]/80 transition duration-500 flex justify-start items-center cursor-pointer py-5 ps-4 pe-5 w-full"
    @click="focusSearchInput"
    ref="searchDiv"
  >
    <input
      type="text"
      name="search"
      placeholder=""
      ref="searchInput"
    />
    <div>
      <img :src="search" class="w-6 h-6 mr-2" alt="Search Icon" />
      <span class="text-white text-xl">Rechercher</span>
    </div>
  </div>
</template>

<style>
.search {
  display: table;
  }
 .search input {
  background: none;
  border: none;
  outline: none;
  width: 28px;
  min-width: 0;
  padding: 0;
  z-index: 1;
  position: relative;
  line-height: 18px;
  margin: 5px 0;
  font-size: 16px;
  border-bottom: 1px solid rgba(255, 255, 255, 0);
  transition: all 0.6s ease;
  cursor: pointer;
  color: white;
  box-sizing: border-box;
  }
 .search input + div {
  position: relative;
  height: 28px;
  width: 100%;
  margin: -28px 0 0 0;
  }
 .search input + div img {
  display: block;
  position: absolute;
  height: 24px;
  width: 24px;
  left: 0;
  top: 0;
  transition: all 0.6s ease;
  }
.search input + div span {
  position: absolute;
  left: 30px;
  top: 0;
  transition: all 0.6s ease;
  }
 .search input:not(:placeholder-shown), .search input:focus {
  width: 100%;
  padding: 0 30px 5px 4px;
  cursor: text;
  border-bottom: 1px solid white;
  transition: all 0.6s ease;
  }
 .search input:not(:placeholder-shown) + div img, .search input:focus + div img {
    left: calc(100% - 30px);
    top: -5px
  }
  .search input:not(:placeholder-shown) + div span, .search input:focus + div span {
    opacity: 0;
    left: calc(100% - 150px);
    transition: all 0.6s ease;
  }
</style>

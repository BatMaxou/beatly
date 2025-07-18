<script lang="ts" setup>
import { ref } from "vue";
import { debounce } from "lodash";
import search from "@/assets/icons/search-light.svg";

const searchInput = ref<HTMLInputElement | null>(null);
const searchDiv = ref<HTMLInputElement | null>(null);
const searchQuery = ref("");
const isSearchActive = ref(false);

const emit = defineEmits(["search", "close-search"]);

const performSearch = (query: string) => {
  isSearchActive.value = true;
  emit("search", query);
};

const debouncedSearch = debounce(performSearch, 500);

function focusSearchInput() {
  searchInput.value?.focus();
  searchDiv.value?.focus();
}

function handleInput(event: Event) {
  const target = event.target as HTMLInputElement;
  let inputValue = target.value;

  if (inputValue.length > 100) {
    inputValue = inputValue.substring(0, 100);
    target.value = inputValue;
  }

  inputValue = inputValue.replace(/[<>\"&]/g, "");

  searchQuery.value = inputValue;

  if (!searchQuery.value.trim()) {
    isSearchActive.value = false;
    emit("close-search");
    debouncedSearch.cancel();
    return;
  }

  debouncedSearch(searchQuery.value.trim());
}

function closeSearch() {
  searchQuery.value = "";
  isSearchActive.value = false;
  searchInput.value!.value = "";
  debouncedSearch.cancel();
  emit("close-search");
}

function handleKeydown(event: KeyboardEvent) {
  if (event.key === "Escape" && isSearchActive.value) {
    closeSearch();
  }
}

defineExpose({
  closeSearch,
});
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
      maxlength="100"
      autocomplete="off"
      spellcheck="false"
      @input="handleInput"
      @keydown="handleKeydown"
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
.search input:not(:placeholder-shown),
.search input:focus {
  width: 100%;
  padding: 0 30px 5px 4px;
  cursor: text;
  border-bottom: 1px solid white;
  transition: all 0.6s ease;
}
.search input:not(:placeholder-shown) + div img,
.search input:focus + div img {
  left: calc(100% - 30px);
  top: -5px;
}
.search input:not(:placeholder-shown) + div span,
.search input:focus + div span {
  opacity: 0;
  left: calc(100% - 150px);
  transition: all 0.6s ease;
}
</style>

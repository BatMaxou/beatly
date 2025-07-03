<script setup lang="ts">
import { useRouter } from "vue-router";

const router = useRouter();

const goToUi = () => {
  router.push("/ui");
};
</script>
<template>
  <button @click="goToUi" class="goto-ui-btn">UI</button>
</template>

<style scoped>
.goto-ui-btn {
  position: fixed;
  bottom: 20px;
  left: 20px;
  padding: 10px 16px;
  background-color: #2c3e50;
  color: white;
  border: none;
  border-radius: 100px;
  cursor: pointer;
  z-index: 1000;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.goto-ui-btn:hover {
  background-color: #1a242f;
}
</style>

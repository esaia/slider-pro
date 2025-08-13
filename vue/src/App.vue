<script setup lang="ts">
import SliderEdit from "@/pages/SliderEdit.vue";
import SlidersTable from "@/pages/SlidersTable.vue";
import { useGlobalStore } from "./store/useGlobal";
import { storeToRefs } from "pinia";
import Toast from "primevue/toast";

const globalStore = useGlobalStore();
const { activeSlider } = storeToRefs(globalStore);
</script>

<template>
  <div class="container">
    <SliderEdit v-if="activeSlider" />
    <SlidersTable v-else />
    <Toast position="bottom-right" group="br" />
  </div>
</template>

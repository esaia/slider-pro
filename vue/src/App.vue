<script setup lang="ts">
import SliderEdit from "@/pages/SliderEdit.vue";
import SlidersTable from "@/pages/SlidersTable.vue";
import { useGlobalStore } from "./store/useGlobal";
import Toast from "primevue/toast";
import { onMounted, ref } from "vue";
import ajaxAxios from "./utils/axios";
import { PER_PAGE } from "./constants/constants";
import Loading from "@components/icons/Loading.vue";

const globalStore = useGlobalStore();

const params = new URLSearchParams(window.location.search);
const sliderId = params.get("slider");

const loading = ref(true);

const fetchSlides = async (page: number = 1) => {
  loading.value = true;

  try {
    if (sliderId) {
      const { data } = await ajaxAxios.post("", {
        action: "slider_pro_get_slider",
        nonce: sliderPro.nonce,
        sliderId
      });

      globalStore.setActiveSlider(data?.data);
    } else {
      const { data } = await ajaxAxios.post("", {
        action: "slider_pro_get_sliders",
        nonce: sliderPro.nonce,
        PER_PAGE,
        page
      });
      globalStore.setSliders(data?.data);
    }
  } catch (error) {
    loading.value = false;
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchSlides();
});
</script>

<template>
  <div class="container">
    <div v-if="loading">
      <Loading />
    </div>
    <SliderEdit v-else-if="sliderId" />
    <SlidersTable v-else @fetch-sliders="(page) => fetchSlides(page)" />
    <Toast position="bottom-right" group="br" />
  </div>
</template>

<script setup lang="ts">
import ajaxAxios from "@/utils/axios";
import { onMounted } from "vue";

const props = defineProps<{
  sliderId?: number;
}>();

onMounted(async () => {
  const { data } = await ajaxAxios.post("", {
    action: "slider_pro_get_slider_shortcode",
    nonce: sliderPro.nonce,
    sliderId: props.sliderId
  });

  console.log("data", data);
});
</script>

<template>
  <div>
    swiper
    {{ sliderId }}
  </div>
</template>

<script setup lang="ts">
import type { Slider } from "@/types/interfaces";
import ajaxAxios from "@/utils/axios";
import { computed, onMounted, ref } from "vue";
import Loading from "@/components/icons/Loading.vue";

import { Swiper, SwiperSlide } from "swiper/vue";
import {
  Navigation,
  Pagination,
  EffectFade,
  EffectCube,
  EffectFlip,
  EffectCoverflow,
  EffectCards,
  EffectCreative
} from "swiper/modules";

import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/effect-fade";
import "swiper/css/effect-cube";
import "swiper/css/effect-flip";
import "swiper/css/effect-coverflow";
import "swiper/css/effect-cards";
import "swiper/css/effect-creative";

const props = defineProps<{
  sliderId?: number;
}>();

const sliderData = ref<Slider>();
const laoding = ref(true);

const modules = [
  Navigation,
  Pagination,
  EffectFade,
  EffectCube,
  EffectFlip,
  EffectCoverflow,
  EffectCards,
  EffectCreative
];

const sliderMeta = computed(() => {
  return sliderData.value?.meta;
});

const slides = computed(() => {
  return sliderMeta.value?.orderBy === "random"
    ? [...(sliderData.value?.slides || [])].sort(() => Math.random() - 0.5)
    : sliderData.value?.slides;
});

const breakpoints = computed(() => {
  console.log("sliderMeta,", sliderMeta.value);
  return {
    0: {
      slidesPerView: sliderMeta.value?.sliderDirection === "vertical" ? 1 : Number(sliderMeta.value?.columns?.mobile)
    },
    800: {
      slidesPerView: sliderMeta.value?.sliderDirection === "vertical" ? 1 : Number(sliderMeta.value?.columns?.tablet)
    },
    1280: {
      slidesPerView: sliderMeta.value?.sliderDirection === "vertical" ? 1 : Number(sliderMeta.value?.columns?.laptop)
    },
    1920: {
      slidesPerView: sliderMeta.value?.sliderDirection === "vertical" ? 1 : Number(sliderMeta.value?.columns?.desktop)
    }
  };
});

const effect = computed(() => {
  return sliderMeta.value?.slideEffect.startsWith("creative") ? "creative" : sliderMeta.value?.slideEffect;
});

const createiveEffect = computed(() => {
  return sliderMeta.value?.slideEffect === "creative1"
    ? {
        prev: {
          shadow: true,
          origin: "left center",
          translate: ["-5%", 0, -200],
          rotate: [0, 100, 0]
        },
        next: {
          origin: "right center",
          translate: ["5%", 0, -200],
          rotate: [0, -100, 0]
        }
      }
    : {
        prev: {
          shadow: true,
          translate: [0, 0, -400]
        },
        next: {
          translate: ["100%", 0, 0]
        }
      };
});

onMounted(async () => {
  const { data } = await ajaxAxios.post("", {
    action: "slider_pro_get_slider_shortcode",
    nonce: sliderPro.nonce,
    sliderId: props.sliderId
  });

  laoding.value = false;

  sliderData.value = data?.data;
});
</script>

<template>
  <div class="overflow-hidden">
    <div v-if="laoding" class="flex items-center justify-center">
      <Loading />
    </div>

    <swiper
      v-else
      :key="JSON.stringify(breakpoints)"
      :modules="modules"
      :creativeEffect="createiveEffect"
      :effect="effect"
      :grabCursor="true"
      :space-between="sliderMeta?.spaceBetween"
      :breakpoints="breakpoints"
      :loop="sliderMeta?.infiniteLoop"
      :auto-height="true"
      :direction="sliderMeta?.sliderDirection"
      class="w-full"
      :style="`--padding-top: ${sliderMeta?.paddingTop}%;`"
    >
      <swiper-slide v-for="slide in slides" :key="slide.url" class="!h-fit">
        <div class="relative w-full" :style="`padding-top: var(--padding-top);`">
          <img :src="slide.url" alt="" class="absolute top-0 left-0 h-full w-full object-cover" />
        </div>
      </swiper-slide>
    </swiper>
  </div>
</template>

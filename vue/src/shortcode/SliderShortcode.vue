<script setup lang="ts">
import type { Slider } from "@/types/interfaces";
import ajaxAxios from "@/utils/axios";
import { computed, onMounted, ref } from "vue";
import Loading from "@/components/icons/Loading.vue";

import { Swiper, SwiperSlide } from "swiper/vue";
import {
  Navigation,
  Pagination,
  Scrollbar,
  Autoplay,
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
import "swiper/css/scrollbar";
import "swiper/css/autoplay";
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
const paginationContainer = ref();
const scrollBarContainer = ref();

const modules = [
  Navigation,
  Pagination,
  Scrollbar,
  Autoplay,
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
  return sliderMeta.value?.slideEffect?.startsWith("creative") ? "creative" : sliderMeta.value?.slideEffect;
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

const autoPlay = computed(() => {
  return sliderMeta.value?.autoplay
    ? {
        delay: sliderMeta.value.autoplayDelay || 3000,
        disableOnInteraction: false,
        pauseOnMouseEnter: sliderMeta.value.pauseonhover,
        reverseDirection: sliderMeta.value.reversedDirection,
        stopOnLastSlide: sliderMeta.value.stopOnLastSlide
      }
    : false;
});

const pagination = computed(() => {
  return sliderMeta.value?.pagination
    ? {
        el: paginationContainer.value,
        clickable: sliderMeta.value.clickable,
        dynamicBullets: sliderMeta.value.paginationStyle === "dynamic",
        renderBullet: (index: number, className: string) => {
          return `<span class='${className}' key="${index}" style='background: #${sliderMeta.value?.paginationActiveColor}' > </span>`;
        }
      }
    : false;
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
  <div class="">
    <div v-if="laoding" class="flex items-center justify-center">
      <Loading />
    </div>

    <swiper
      v-else
      :key="JSON.stringify(breakpoints)"
      :modules="modules"
      :creativeEffect="createiveEffect"
      :effect="effect"
      :space-between="sliderMeta?.spaceBetween"
      :breakpoints="breakpoints"
      :loop="sliderMeta?.infiniteLoop"
      :direction="sliderMeta?.sliderDirection"
      :centered-slides="sliderMeta?.centerSlides"
      :autoplay="autoPlay"
      :pagination="pagination"
      :scrollbar="{
        el: scrollBarContainer
      }"
      grabCursor
      auto-height
      class="w-full"
      :style="`--padding-top: ${sliderMeta?.paddingTop}%;`"
    >
      <swiper-slide v-for="slide in slides" :key="slide.url" class="!h-fit">
        <div class="relative w-full" :style="`padding-top: var(--padding-top);`">
          <img :src="slide.url" alt="" class="absolute top-0 left-0 h-full w-full object-cover" />
        </div>
      </swiper-slide>
    </swiper>

    <div
      class="flex w-full items-center justify-center"
      :style="{
        margin: `${sliderMeta?.paginationMargin.top || 0}px ${sliderMeta?.paginationMargin.right || 0}px ${sliderMeta?.paginationMargin.down || 0}px ${sliderMeta?.paginationMargin.left || 0}px`
      }"
    >
      <div ref="paginationContainer" :class="{ '!w-fit': sliderMeta?.paginationStyle !== 'dynamic' }" />
    </div>

    <div
      class="flex w-full items-center justify-center"
      :style="{
        margin: `${sliderMeta?.paginationMargin.top || 0}px ${sliderMeta?.paginationMargin.right || 0}px ${sliderMeta?.paginationMargin.down || 0}px ${sliderMeta?.paginationMargin.left || 0}px`
      }"
    >
      <div ref="scrollBarContainer" />
    </div>
  </div>
</template>

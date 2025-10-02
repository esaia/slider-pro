<script setup lang="ts">
import type { Slider } from "@/types/interfaces";
import ajaxAxios from "@/utils/axios";
import { computed, nextTick, onMounted, ref } from "vue";
import Loading from "@/components/icons/Loading.vue";
// @ts-ignore
import FancyBoxComp from "@/shortcode/FancyBoxComp.vue";

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
import NextArrowIcon from "@/components/icons/NextArrowIcon.vue";

const props = defineProps<{
  sliderId?: number;
}>();

const sliderData = ref<Slider>();
const laoding = ref(true);
const paginationContainer = ref();
const scrollBarContainer = ref();

const navigationPrev = ref();
const navigationNext = ref();

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

const isSliderDirectionVertical = computed(() => {
  return sliderMeta.value?.sliderDirection === "vertical";
});

const isClickActionLightbox = computed(() => {
  return sliderMeta.value?.clickAction === "lightbox";
});

const isSliderPerViewOne = computed(() => {
  return (
    isSliderDirectionVertical.value ||
    (sliderMeta.value?.slideEffect && !["slide", "coverflow"].includes(sliderMeta.value?.slideEffect.toLowerCase()))
  );
});

const breakpoints = computed(() => {
  console.log("sliderMeta,", sliderMeta.value);
  return {
    0: {
      slidesPerView: isSliderPerViewOne.value ? 1 : Number(sliderMeta.value?.columns?.mobile)
    },
    800: {
      slidesPerView: isSliderPerViewOne.value ? 1 : Number(sliderMeta.value?.columns?.tablet)
    },
    1280: {
      slidesPerView: isSliderPerViewOne.value ? 1 : Number(sliderMeta.value?.columns?.laptop)
    },
    1920: {
      slidesPerView: isSliderPerViewOne.value ? 1 : Number(sliderMeta.value?.columns?.desktop)
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
        delay: Number(sliderMeta.value.autoplayDelay) || 3000,
        disableOnInteraction: false,
        pauseOnMouseEnter: sliderMeta.value.pauseonhover,
        reverseDirection: sliderMeta.value.reversedDirection,
        stopOnLastSlide: sliderMeta.value.stopOnLastSlide
      }
    : false;
});

const pagination = computed(() => {
  return sliderMeta.value?.pagination && ["bullets", "dynamic", "fraction"].includes(sliderMeta.value.paginationStyle)
    ? {
        el: paginationContainer.value,
        clickable: sliderMeta.value.clickable,
        dynamicBullets: sliderMeta.value.paginationStyle === "dynamic",
        type: sliderMeta.value.paginationStyle === "fraction" ? ("fraction" as "fraction") : ("bullets" as "bullets"),
        renderBullet: (index: number, className: string) => {
          return `<span class='${className}' key="${index}" style='background: #${sliderMeta.value?.paginationActiveColor}' > </span>`;
        }
      }
    : false;
});

const scrollbar = computed(() => {
  return sliderMeta.value?.pagination && sliderMeta.value?.paginationStyle === "scrollbar"
    ? {
        el: scrollBarContainer.value
      }
    : false;
});

const navigation = computed(() => {
  return sliderMeta.value?.navigation
    ? {
        prevEl: navigationPrev.value,
        nextEl: navigationNext.value
      }
    : false;
});

const paginationMargin = computed(() => {
  return `${sliderMeta.value?.paginationMargin?.top || 0}px ${sliderMeta.value?.paginationMargin?.right || 0}px ${sliderMeta.value?.paginationMargin?.down || 0}px ${sliderMeta.value?.paginationMargin?.left || 0}px`;
});

const onSwiper = async (swiper: any) => {
  await nextTick();

  // Update navigation elements
  if (navigationPrev.value && navigationNext.value) {
    swiper.navigation.init();
    swiper.navigation.update();
  }
};

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
  <div class="slider-pro relative">
    <fancy-box-comp :options="{}">
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
        :scrollbar="scrollbar"
        :navigation="navigation"
        grabCursor
        auto-height
        :style="`--padding-top: ${sliderMeta?.paddingTop}%;`"
        @swiper="onSwiper"
      >
        <swiper-slide v-for="slide in slides" :key="slide.url" class="!h-fit">
          <div class="relative w-full overflow-hidden" :style="{ paddingTop: 'var(--padding-top)' }">
            <component
              :is="isClickActionLightbox ? 'a' : 'div'"
              :data-fancybox="isClickActionLightbox ? 'gallery' : null"
              :href="slide.url"
            >
              <img
                :src="slide.url"
                alt=""
                class="absolute top-0 left-0 h-full w-full object-cover transition-all duration-500 hover:scale-110"
              />
            </component>
          </div>
        </swiper-slide>
        <div
          v-show="sliderMeta?.navigation"
          :style="`--swiper-pro-nav-color: #${sliderMeta?.navigationActiveColor};`"
          class="pointer-events-none absolute top-1/2 left-0 z-20 flex w-full -translate-y-1/2 items-center justify-between [&_.swiper-button-disabled]:opacity-50 [&_path]:fill-[var(--swiper-pro-nav-color)] [&_svg]:size-8"
        >
          <div ref="navigationPrev" class="pointer-events-auto rotate-180 cursor-pointer transition-all">
            <NextArrowIcon />
          </div>
          <div ref="navigationNext" class="pointer-events-auto cursor-pointer transition-all">
            <NextArrowIcon />
          </div>
        </div>
      </swiper>

      <div
        v-show="sliderMeta?.pagination && sliderMeta?.paginationStyle === 'scrollbar'"
        :class="{
          '!absolute top-1/2 right-4 z-20 h-full !w-[5px] -translate-y-1/2 py-4': isSliderDirectionVertical
        }"
        :style="{
          margin: paginationMargin
        }"
      >
        <div
          ref="scrollBarContainer"
          class="h-[4px] rounded-full bg-black [.slider-pro_&_.swiper-scrollbar-drag]:!bg-[var(--scrollbar-active)]"
          :class="{
            'h-full': isSliderDirectionVertical
          }"
          :style="{
            '--scrollbar-active': `#${sliderMeta?.paginationActiveColor}`,
            background: `#${sliderMeta?.scrollbarBackground}`
          }"
        ></div>
      </div>

      <div
        v-show="sliderMeta?.pagination"
        class="relative z-20 flex w-full items-center justify-center"
        :class="{
          '!absolute top-1/2 right-4 !w-fit -translate-y-1/2': isSliderDirectionVertical
        }"
        :style="{
          margin: paginationMargin
        }"
      >
        <div
          ref="paginationContainer"
          class="!transform-[unset]"
          :class="{ '!w-fit': sliderMeta?.paginationStyle !== 'dynamic' }"
        />
      </div>
    </fancy-box-comp>
  </div>
</template>

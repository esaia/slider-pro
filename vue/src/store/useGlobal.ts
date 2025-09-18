import type { Slide, SlidersDataInterface } from "@/types/interfaces";
import { defineStore } from "pinia";
import { computed, ref } from "vue";

export const useGlobalStore = defineStore("global", () => {
  const sliders = ref<SlidersDataInterface>();

  const metadata = ref({
    // General settings
    slideEffect: "",
    columns: { desktop: 1, laptop: 1, tablet: 1, mobile: 1 },
    paddingTop: 50,
    spaceBetween: 0,
    clickAction: "lightbox",
    orderBy: "drag",
    infiniteLoop: false,
    sliderDirection: "horizontal",
    centerSlides: false,
    // Module settings

    // Autoplay
    autoplay: false,
    autoplayDelay: 3000,
    reversedDirection: false,
    pauseonhover: true,
    stopOnLastSlide: false,

    // Navigation
    navigation: false,
    navigationActiveColor: "000000",

    // Pagination
    pagination: false,
    paginationStyle: "bullets",
    scrollbarBackground: "cccccc",
    paginationActiveColor: "cccccc",
    clickable: false,
    paginationMargin: { top: 0, right: 0, down: 0, left: 0 }
  });

  const activeSlider = computed(() => {
    const params = new URLSearchParams(window.location.search);

    const sliderId = params.get("slider");

    if (!sliderId) return;

    return sliders.value?.data.find((i) => i.id.toString() === sliderId?.toString());
  });

  const updateMetadata = (key: string, value: any) => {
    const [parent, child] = key.split(".");
    if (child && Object.prototype.hasOwnProperty.call((metadata.value as any)[parent], child)) {
      (metadata.value as any)[parent][child] = ["columns", "margin"].includes(parent) ? Number(value) : value;
    } else {
      (metadata.value as any)[key] = value;
    }
  };

  const setSliders = (sliderParam: SlidersDataInterface) => {
    sliders.value = sliderParam;
  };

  const setSlides = (slides: Slide[]) => {
    if (sliders.value && sliders.value.data) {
      sliders.value.data = sliders.value.data.map((i) => {
        return i.id === activeSlider.value?.id ? { ...i, slides } : i;
      });
    }
  };

  // TEMPORARY COMMENT: I doubt we don't need it.
  // watch(
  //   () => activeSlider.value,
  //   () => {
  //     if (activeSlider.value?.meta && Object.keys(activeSlider.value?.meta).length) {
  //       metadata.value = activeSlider.value?.meta;
  //     }
  //   },
  //   {
  //     deep: true
  //   }
  // );

  return {
    // State
    sliders,
    metadata,
    // Geter
    activeSlider,
    // Setters
    updateMetadata,
    setSliders,
    setSlides
  };
});

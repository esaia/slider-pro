import { defineStore } from "pinia";
import { ref } from "vue";

export const useProjectStore = defineStore("project", () => {
  const metadata = ref<any>({
    // General settings
    slideEffect: "fade",
    columns: { desktop: 1, laptop: 1, tablet: 1, mobile: 1 },
    spaceBetween: 0,
    clickAction: "lightbox",
    orderBy: "drag",
    infiniteLoop: false,
    sliderDirection: "right",
    // Module settings

    // Autoplay
    autoplay: false,
    sliderSpeed: 3000,
    sliderOrientation: "horizontal",
    pauseonhover: true,

    // Navigation
    navigation: true,
    navigationPosition: "vertical-outer",
    navigationColors: { color: "#ffffff", active: "#cccccc" },

    // Pagination
    pagination: true,
    paginationStyle: "bullets",
    paginationColors: { color: "#ffffff", active: "#cccccc" },
    margin: { top: 0, right: 0, down: 0, left: 0 }
  });

  const updateMetadata = (key: string, value: any) => {
    const [parent, child] = key.split(".");
    if (child && Object.prototype.hasOwnProperty.call(metadata.value[parent], child)) {
      metadata.value[parent][child] = ["columns", "margin"].includes(parent) ? Number(value) : value;
    } else if (Object.prototype.hasOwnProperty.call(metadata.value, key)) {
      metadata.value[key] = value;
    } else {
      console.warn(`Property ${key} does not exist in metadata`);
    }
  };

  return {
    metadata,
    updateMetadata
  };
});

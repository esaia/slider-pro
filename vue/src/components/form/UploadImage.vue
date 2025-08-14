<script setup lang="ts">
import { onMounted, ref, watch } from "vue";
import { useGlobalStore } from "@/store/useGlobal";
import { useSelectImage } from "@/composables/useSelectImage";
import TrashIcon from "@/components/icons/TrashIcon.vue";
import GalleryIcon from "@/components/icons/GalleryIcon.vue";
import PencliIcon from "@/components/icons/PencliIcon.vue";
import { createSwapy, type Swapy } from "swapy";

import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Dialog from "primevue/dialog";
import Textarea from "primevue/textarea";
import type { ImageInterface, Slide } from "@/types/interfaces";
import { storeToRefs } from "pinia";

const props = defineProps<{
  required?: boolean;
  multiple?: boolean;
}>();

const globalStore = useGlobalStore();
const { activeSlider } = storeToRefs(globalStore);
const { selectedImages, selectImage } = useSelectImage(props.multiple || false);

const hoverIcons = [PencliIcon, TrashIcon];

let swapyInstance: null | Swapy = null;

const container = ref();
const isModalOpen = ref(false);
const data = ref<Slide[]>(activeSlider.value?.slides || []);
const activeSlide = ref<Slide | null>(null);

const handleClickIcon = (index: number, img: string) => {
  if (index === 0) {
    isModalOpen.value = true;
    activeSlide.value = data.value.find((i) => i.url === img) || null;
  } else if (index === 1) {
    selectedImages.value = selectedImages.value?.filter((image) => image.url !== img) || [];
  }
};

const updateSlide = () => {
  if (activeSlide.value) {
    const index = data.value.findIndex((item) => item.url === activeSlide.value?.url);
    if (index !== -1) {
      data.value[index] = { ...activeSlide.value };
    }
  }
  isModalOpen.value = false;
  activeSlide.value = null;
};

const initializeSwapy = () => {
  if (swapyInstance) {
    swapyInstance.destroy();
    swapyInstance = null;
  }

  if (!container.value || data.value.length === 0) return;

  swapyInstance = createSwapy(container.value, {
    animation: "dynamic"
  });

  swapyInstance.onSwapEnd((event) => {
    const sortedImgUrls = Object.values(event.slotItemMap.asObject);
    const arr: any = [];
    sortedImgUrls.forEach((url) => {
      const findedItem = data.value.find((i) => i.url === url);
      if (!findedItem) return;
      arr.push(findedItem);
    });
    data.value = arr;
  });
};

watch(
  () => selectedImages.value,
  () => {
    const urls = selectedImages.value?.map((img) => img.url);

    data.value =
      [...new Set(urls)]?.map((url) => {
        const findData = data.value.find((slide) => slide.url === url);
        return { ...findData, url };
      }) || [];
  },
  {
    deep: true
  }
);

// Watch data changes and reinitialize Swapy
watch(
  () => data.value,
  () => {
    if (data.value) {
      globalStore.setSlides(data.value);
    }
    initializeSwapy();
  },
  {
    deep: true,
    flush: "post" // Ensure DOM updates are complete
  }
);

onMounted(() => {
  initializeSwapy();
  selectedImages.value = data.value as ImageInterface[];
});
</script>

<template>
  <div ref="container" class="flex flex-wrap items-center gap-3">
    <div
      class="flex size-36 cursor-pointer flex-col items-center justify-center gap-2 rounded-sm bg-green-500 transition-all hover:scale-105"
      @click="selectImage"
    >
      <GalleryIcon class="[&_path]:stroke-white" />
      <div class="text-lg text-white">ADD IMAGE</div>
    </div>

    <div
      v-for="(item, index) in data"
      :key="item.url + index"
      :data-swapy-slot="`slot-${index}`"
      class="group relative cursor-all-scroll"
    >
      <div :data-swapy-item="item.url">
        <div class="absolute top-2 right-2 z-10 flex items-center gap-2">
          <div
            v-for="(icon, i) in hoverIcons"
            :key="i"
            class="scale-0 cursor-pointer rounded-[999px] bg-white p-2 opacity-0 transition-all group-hover:scale-100 group-hover:opacity-100 hover:scale-105 [&_svg]:size-4"
            @click="handleClickIcon(i, item.url)"
          >
            <component :is="icon" />
          </div>
        </div>

        <img :src="item.url" class="size-36 rounded-sm object-cover" />
      </div>
    </div>

    <Dialog v-model:visible="isModalOpen" modal header="Slide configuration" :style="{ width: '50rem' }">
      <div v-if="activeSlide" class="flex gap-4">
        <img :src="activeSlide.url" class="size-48 object-cover" />

        <div class="flex flex-1 flex-col gap-4">
          <InputText v-model="activeSlide.title" placeholder="title" class="w-full" />
          <Textarea v-model="activeSlide.description" placeholder="description" class="w-full" />
          <Button label="Update" class="mt-4 w-full" @click="updateSlide" />
        </div>
      </div>
    </Dialog>
  </div>
</template>

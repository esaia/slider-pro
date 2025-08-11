<script setup lang="ts">
import { useSelectImage } from "@/composables/useSelectImage";
import { computed } from "vue";
import TrashIcon from "@/components/icons/TrashIcon.vue";
import GalleryIcon from "@/components/icons/GalleryIcon.vue";

const props = defineProps<{
  title: string;
  required?: boolean;
  multiple?: boolean;
}>();

const { selectedImages, selectImage } = useSelectImage(props.multiple || false);

const images = computed(() => {
  if (!selectedImages.value) return [];

  const urls = selectedImages.value.map((img) => img.url);
  return [...new Set(urls)];
});
</script>
<template>
  <div class="flex items-center gap-3">
    <div
      class="flex size-36 cursor-pointer flex-col items-center justify-center gap-2 rounded-sm bg-green-600 transition-all hover:scale-105"
      @click="selectImage"
    >
      <GalleryIcon class="[&_path]:stroke-white" />
      <div class="text-lg text-white">ADD IMAGE</div>
    </div>

    <div v-for="image in images" :key="image" class="group relative cursor-all-scroll">
      <div class="absolute top-2 right-2 flex items-center gap-2">
        <div
          class="cursor-pointer rounded-full bg-white p-2 opacity-0 transition-all group-hover:opacity-100 hover:scale-105 [&_svg]:size-6"
        >
          <TrashIcon />
        </div>
      </div>
      <img :src="image" alt="" class="size-36 rounded-sm object-cover" />
    </div>
  </div>
</template>

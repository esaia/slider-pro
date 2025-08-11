<script setup lang="ts">
import { useSelectImage } from "@/composables/useSelectImage";
import { computed } from "vue";
import Button from "primevue/button";

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
  <div>
    <i class="pi pi-check" style="font-size: 1rem"></i>

    <Button label="Upload" class="w-fit" variant="outlined" @click="selectImage" />

    <div class="mt-4 flex items-center gap-3">
      <div v-for="image in images" :key="image" class="relative">
        <div class="absolute top-0 right-0">icon</div>
        <img :src="image" alt="" class="h-20 w-20 object-cover" />
      </div>
    </div>
  </div>
</template>

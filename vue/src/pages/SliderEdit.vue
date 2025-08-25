<script setup lang="ts">
import SliderSettingsTabs from "@/components/UI/SliderSettingsTabs.vue";
import UploadImage from "@/components/form/UploadImage.vue";
import { useGlobalStore } from "@/store/useGlobal";
import { storeToRefs } from "pinia";
import { ref, watch } from "vue";
import ajaxAxios from "@/utils/axios";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import { useToast } from "primevue/usetoast";
import SliderShortcode from "@/shortcode/SliderShortcode.vue";

const toast = useToast();

const globalStore = useGlobalStore();
const { metadata, activeSlider } = storeToRefs(globalStore);

const title = ref(activeSlider.value?.title || "");
const loading = ref(false);
const isPreview = ref(false);
const previewKey = ref(0);

const handleUpdateSlider = async () => {
  try {
    loading.value = true;
    await ajaxAxios.post("", {
      action: "slider_pro_update_slider",
      nonce: sliderPro.nonce,
      sliderId: activeSlider.value?.id,
      title: title.value,
      slides: activeSlider.value?.slides
    });

    await ajaxAxios.post("", {
      action: "slider_pro_update_slider_meta_bulk",
      nonce: sliderPro.nonce,
      sliderId: activeSlider.value?.id,
      meta: metadata.value
    });

    previewKey.value++;

    toast.add({ severity: "success", summary: "Slider updated", detail: "", group: "br", life: 3000 });
  } catch (error) {
    toast.add({ severity: "error", summary: "Something went wrong", detail: "", group: "br", life: 3000 });
  } finally {
    loading.value = false;
  }
};

watch(
  () => isPreview.value,
  () => {
    if (isPreview.value) {
      window.scrollTo({
        top: 0,
        behavior: "smooth"
      });
    }
  }
);
</script>
<template>
  <div class="container space-y-6">
    <div class="fixed right-10 bottom-10 z-[99]">
      <Button severity="info" raised @click="isPreview = !isPreview">
        {{ isPreview ? "hide preview" : "show preview" }}
      </Button>
    </div>

    <div v-if="isPreview" :key="previewKey">
      <SliderShortcode :slider-id="activeSlider?.id" />
    </div>

    <div>
      <a :href="`${sliderPro.plugin_url}`">
        <Button label="Back to sliders list" variant="link" class="mb-2 w-fit !p-0" />
      </a>

      <div class="flex items-center gap-3">
        <InputText v-model="title" placeholder="title" class="w-full" />

        <span>shortcode:</span>
        <InputText type="text" :value="`[slider-pro id='${activeSlider?.id}']`" />

        <Button label="Save" class="w-32" :loading="loading" @click="handleUpdateSlider" />
      </div>
    </div>

    <div class="bg-white shadow-sm">
      <div class="flex w-full items-center justify-between bg-gradient-to-r from-teal-300 to-teal-500 p-4 text-white">
        <div class="text-2xl">Slider Pro</div>

        <Button label="get support" variant="link" class="[&_span]:text-white" />
      </div>

      <UploadImage required multiple class="p-4" />
    </div>

    <SliderSettingsTabs />
  </div>
</template>

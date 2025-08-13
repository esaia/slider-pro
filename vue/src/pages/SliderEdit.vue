<script setup lang="ts">
import SliderSettingsTabs from "@/components/UI/SliderSettingsTabs.vue";
import UploadImage from "@/components/form/UploadImage.vue";
import { useGlobalStore } from "@/store/useGlobal";
import { storeToRefs } from "pinia";
import { ref } from "vue";
import ajaxAxios from "@/utils/axios";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import { useToast } from "primevue/usetoast";

const toast = useToast();

const globalStore = useGlobalStore();
const { metadata, activeSlider } = storeToRefs(globalStore);

const title = ref(activeSlider.value?.title || "");

const loading = ref(false);

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

    toast.add({ severity: "success", summary: "Slider updated", detail: "", group: "br", life: 3000 });
  } catch (error) {
    console.error("ERROR", error);
    toast.add({ severity: "error", summary: "Something went wrong", detail: "", group: "br", life: 3000 });
  } finally {
    loading.value = false;
  }
};
</script>
<template>
  <div class="container space-y-6">
    <pre>
      {{ metadata }}
    </pre>
    <div class="flex items-center gap-3">
      <InputText v-model="title" placeholder="title" class="w-full" />

      <Button label="Save" class="w-fit" :loading="loading" @click="handleUpdateSlider" />
    </div>

    <div class="bg-white shadow-sm">
      <div class="flex w-full items-center justify-between bg-gradient-to-r from-teal-300 to-teal-500 p-4 text-white">
        <div class="text-2xl">Slider Pro</div>

        <Button label="get support" variant="link" class="[&_span]:text-white" />
      </div>

      <UploadImage title="test title" required multiple class="p-4" />
    </div>

    <SliderSettingsTabs />
  </div>
</template>

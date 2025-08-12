<script setup lang="ts">
import SliderSettingsTabs from "@/components/UI/SliderSettingsTabs.vue";
import UploadImage from "@/components/form/UploadImage.vue";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import { useProjectStore } from "@/store/useGlobal";
import { storeToRefs } from "pinia";

const globalStore = useProjectStore();
const { metadata } = storeToRefs(globalStore);
</script>
<template>
  <div class="container space-y-6">
    <pre>
      {{ metadata }}
    </pre>
    <div class="flex items-center gap-3">
      <InputText placeholder="title" class="w-full" />

      <Button label="Save" class="w-fit" />
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

<script setup lang="ts">
import UploadImage from "@/components/form/UploadImage.vue";
import InputText from "primevue/InputText";
import Button from "primevue/button";
</script>
<template>
  <div class="container">
    <InputText placeholder="title" class="w-full" />

    <Button label="Save" class="w-fit" />

    <div class="my-10 rounded-md bg-white p-2 shadow-sm">
      <UploadImage title="test title" required multiple />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Dialog from "primevue/dialog";

const products = [
  {
    id: "1000",
    name: "Slider Pro 1",
    shortcode: "[slider-pro id='1000']",
    date: "2023-10-01"
  },
  {
    id: "1001",
    name: "Slider Pro 2",
    shortcode: "[slider-pro id='1001']",
    date: "2023-10-02"
  },
  {
    id: "1002",
    name: "Slider Pro 3",
    shortcode: "[slider-pro id='1002']",
    date: "2023-10-03"
  },
  {
    id: "1003",
    name: "Slider Pro 4",
    shortcode: "[slider-pro id='1003']",
    date: "2023-10-04"
  },
  {
    id: "1004",
    name: "Slider Pro 5",
    shortcode: "[slider-pro id='1004']",
    date: "2023-10-05"
  }
];

const isModalOpen = ref(false);
</script>

<template>
  <div class="container">
    <div class="mb-6 flex items-center justify-between">
      <div class="text-2xl font-semibold">Slider Pro</div>
      <Button label="Add slider" class="w-fit" @click="isModalOpen = true" />
    </div>

    <DataTable :value="products" tableStyle="min-width: 50rem" removableSort>
      <Column field="name" header="Name" sortable></Column>

      <Column field="shortcode" header="Shortcode">
        <template #body="slotProps">
          <InputText type="text" :value="slotProps.data.shortcode" />
        </template>
      </Column>

      <Column field="date" header="Date" sortable></Column>
      <Column header="Action">
        <template #body>
          <Button label="Open" class="w-fit" variant="text" size="small" />
        </template>
      </Column>
    </DataTable>
  </div>

  <Dialog v-model:visible="isModalOpen" modal header="Create Slider" :style="{ width: '25rem' }">
    <div>
      <InputText placeholder="title" class="w-full" />
      <Button label="Create" class="mt-4 w-full" />
    </div>
  </Dialog>
</template>

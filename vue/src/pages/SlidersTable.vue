<script setup lang="ts">
import { onMounted, ref } from "vue";
import Button from "primevue/button";
import InputText from "primevue/inputtext";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Dialog from "primevue/dialog";
import ajaxAxios from "@/utils/axios";
import { useGlobalStore } from "@/store/useGlobal";
import { storeToRefs } from "pinia";
import Loading from "@/components/icons/Loading.vue";

const globalStore = useGlobalStore();
const { sliders } = storeToRefs(globalStore);

const perPage = 10;
const first = ref(1);

const isCreateModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const title = ref("");
const errorMsg = ref("");
const loading = ref(true);
const activeSlideId = ref();

const handleCreateSlider = async () => {
  errorMsg.value = "";
  try {
    await ajaxAxios.post("", {
      action: "slider_pro_create_slider",
      nonce: sliderPro.nonce,
      title: title.value
    });

    title.value = "";
    isCreateModalOpen.value = false;
    fetchSlides();
  } catch (error) {
    errorMsg.value = "something went wrong";
  }
};

const handleDeleteSlider = async () => {
  errorMsg.value = "";

  try {
    await ajaxAxios.post("", {
      action: "slider_pro_delete_slider",
      nonce: sliderPro.nonce,
      sliderId: activeSlideId.value
    });

    isDeleteModalOpen.value = false;
    fetchSlides();
  } catch (error) {
    errorMsg.value = "something went wrong";
  }
};

const handleClickDeleteBtn = (id: number) => {
  activeSlideId.value = id;
  isDeleteModalOpen.value = true;
};

const handlePageChange = (e: any) => {
  first.value = e.first;

  fetchSlides(e.page + 1);
};

const fetchSlides = async (page: number = 1) => {
  loading.value = true;
  const { data } = await ajaxAxios.post("", {
    action: "slider_pro_get_sliders",
    nonce: sliderPro.nonce,
    perPage,
    page
  });

  loading.value = false;

  globalStore.setSliders(data?.data);
};

onMounted(() => {
  fetchSlides();
});
</script>

<template>
  <div v-if="loading">
    <Loading />
  </div>
  <div v-else>
    <div class="mb-6 flex items-center justify-between">
      <div class="text-2xl font-semibold">Slider Pro</div>
      <Button label="Add slider" class="w-fit" @click="isCreateModalOpen = true" />
    </div>

    <DataTable
      v-if="sliders"
      v-model:first="first"
      :value="sliders?.data"
      tableStyle="min-width: 50rem"
      removableSort
      :paginator="sliders.total > sliders.per_page"
      :rows="perPage"
      :totalRecords="sliders.total"
      lazy
      @page="handlePageChange"
    >
      <Column field="title" header="Title"></Column>

      <Column field="shortcode" header="Shortcode">
        <template #body="slotProps">
          <InputText type="text" :value="`[slider-pro id='${slotProps.data.id}']`" />
        </template>
      </Column>

      <Column field="created_at" header="Date"></Column>
      <Column header="Action">
        <template #body="slotProps">
          <a :href="`${sliderPro.plugin_url}&slider=${slotProps.data.id}`">
            <Button label="Open" class="w-fit" variant="text" size="small" />
          </a>

          <Button
            label="Delete"
            class="w-fit"
            variant="text"
            severity="danger"
            size="small"
            @click="handleClickDeleteBtn(slotProps.data.id)"
          />
        </template>
      </Column>
    </DataTable>
  </div>

  <Dialog v-model:visible="isCreateModalOpen" modal header="Create Slider" :style="{ width: '25rem' }">
    <div>
      <InputText v-model="title" placeholder="title" class="w-full" />
      <Button label="Create" class="mt-4 w-full" @click="handleCreateSlider" />
      <p v-if="errorMsg" class="p-2 font-semibold text-red-500">{{ errorMsg }}</p>
    </div>
  </Dialog>

  <Dialog v-model:visible="isDeleteModalOpen" modal header="Delete Slider" :style="{ width: '25rem' }">
    <div>
      <p>Are you sure to delete slider with id {{ activeSlideId }}</p>
      <Button label="Delete" class="mt-4 w-full" severity="danger" @click="handleDeleteSlider" />
      <p v-if="errorMsg" class="p-2 font-semibold text-red-500">{{ errorMsg }}</p>
    </div>
  </Dialog>
</template>

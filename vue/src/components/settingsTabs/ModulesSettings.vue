<script setup lang="ts">
import { ALL_DIRECTIONS, DIRECTIONS, NAVIGATION_POSITIONS, PAGINATION_STYLES } from "@/constants/constants";
import Config from "@components/UI/Config.vue";
import ToggleButton from "primevue/togglebutton";
import InputText from "primevue/inputtext";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import ColorPicker from "primevue/colorpicker";
import SelectButton from "primevue/selectbutton";
import Select from "primevue/select";

import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";

import { useGlobalStore } from "@/store/useGlobal";
import { storeToRefs } from "pinia";

const globalStore = useGlobalStore();
const { metadata } = storeToRefs(globalStore);
</script>
<template>
  <Tabs value="0">
    <TabList>
      <Tab value="0">Autoplay</Tab>
      <Tab value="1">Navigation</Tab>
      <Tab value="2">Pagination</Tab>
    </TabList>
    <TabPanels>
      <TabPanel value="0">
        <div class="space-y-6">
          <Config title="AutoPlay" desc="Select a slide transition effect.">
            <ToggleButton
              :model-value="metadata.autoplay"
              onLabel="On"
              offLabel="Off"
              @update:model-value="globalStore.updateMetadata('autoplay', $event)"
            />
          </Config>

          <Config title="Slider speed" desc="Set autoplay scroll speed in millisecond.">
            <InputGroup>
              <InputText
                :model-value="metadata.sliderSpeed"
                type="number"
                class="!w-24"
                @update:model-value="globalStore.updateMetadata('sliderSpeed', $event)"
              />
              <InputGroupAddon> MS </InputGroupAddon>
            </InputGroup>
          </Config>

          <Config title="Slider Direction" desc="Set slider direction as you need.">
            <SelectButton
              :model-value="metadata.sliderDirection"
              :options="DIRECTIONS"
              size="small"
              optionLabel="name"
              option-value="value"
              @update:model-value="globalStore.updateMetadata('sliderDirection', $event)"
            />
          </Config>

          <Config title="Pause on Hover" desc="Enable/Disable carousel pause on hover.">
            <ToggleButton
              :model-value="metadata.pauseonhover"
              onLabel="On"
              offLabel="Off"
              @update:model-value="globalStore.updateMetadata('pauseonhover', $event)"
            />
          </Config>
        </div>
      </TabPanel>

      <TabPanel value="1">
        <div class="space-y-6">
          <Config title="Navigation" desc="Enable navigation.">
            <ToggleButton
              :model-value="metadata.navigation"
              onLabel="On"
              offLabel="Off"
              @update:model-value="globalStore.updateMetadata('navigation', $event)"
            />
          </Config>

          <Config title="Navigation position" desc="Select a position for the navigation arrows.">
            <Select
              :model-value="metadata.navigationPosition"
              :options="NAVIGATION_POSITIONS"
              optionLabel="name"
              placeholder="Select a navigation position"
              size="small"
              class="w-full md:w-56"
              option-value="value"
              @update:model-value="globalStore.updateMetadata('navigationPosition', $event)"
            />
          </Config>

          <Config title="Navigation Color" desc="Set color for the slider navigation.">
            <div class="flex items-center gap-4">
              <div class="flex flex-col items-start gap-2 text-gray-500">
                <label> Color:</label>
                <ColorPicker
                  :model-value="metadata.navigationColors.color"
                  name="color"
                  @update:model-value="globalStore.updateMetadata('navigationColors.color', $event)"
                />
              </div>
              <div class="flex flex-col items-start gap-2 text-gray-500">
                <label>Active color:</label>

                <ColorPicker
                  :model-value="metadata.navigationColors.active"
                  name="color"
                  @update:model-value="globalStore.updateMetadata('navigationColors.active', $event)"
                />
              </div>
            </div>
          </Config>
        </div>
      </TabPanel>

      <TabPanel value="2">
        <div class="space-y-6">
          <Config title="Enable pagination" desc="Show slider pagination.">
            <ToggleButton
              :model-value="metadata.pagination"
              onLabel="On"
              offLabel="Off"
              @update:model-value="globalStore.updateMetadata('pagination', $event)"
            />
          </Config>

          <Config title="Pagination Style" desc="Choose pagination style.">
            <SelectButton
              :model-value="metadata.paginationStyle"
              :options="PAGINATION_STYLES"
              size="small"
              optionLabel="name"
              option-value="value"
              @update:model-value="globalStore.updateMetadata('paginationStyle', $event)"
            />
          </Config>

          <Config title="Pagination Color" desc="Set color for the slider pagination.">
            <div class="flex items-center gap-4">
              <div class="flex flex-col items-start gap-2 text-gray-500">
                <label> Color:</label>
                <ColorPicker
                  :model-value="metadata.paginationColors.color"
                  name="color"
                  @update:model-value="globalStore.updateMetadata('paginationColors.color', $event)"
                />
              </div>
              <div class="flex flex-col items-start gap-2 text-gray-500">
                <label>Active color:</label>

                <ColorPicker
                  :model-value="metadata.paginationColors.active"
                  name="color"
                  @update:model-value="globalStore.updateMetadata('paginationColors.active', $event)"
                />
              </div>
            </div>
          </Config>

          <Config title="Pagination margin" desc="Set margin for slider pagination.">
            <div class="flex items-center gap-2">
              <InputGroup v-for="direction in ALL_DIRECTIONS" :key="direction">
                <InputGroupAddon> {{ direction }} </InputGroupAddon>
                <InputText
                  :model-value="metadata.paginationMargin[direction]"
                  type="number"
                  class="!w-20"
                  @update:model-value="globalStore.updateMetadata(`paginationMargin.${direction}`, $event)"
                />
              </InputGroup>
            </div>
          </Config>
        </div>
      </TabPanel>
    </TabPanels>
  </Tabs>
</template>

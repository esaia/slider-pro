<script setup lang="ts">
import { ALL_DIRECTIONS, NAVIGATION_POSITIONS, ORIENTATION, PAGINATION_STYLES } from "@/constants/constants";
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

          <Config title="Slider autoplay delay" desc="Set autoplay scroll speed delay in millisecond.">
            <InputGroup>
              <InputText
                :model-value="metadata.autoplayDelay"
                type="number"
                class="!w-24"
                @update:model-value="globalStore.updateMetadata('autoplayDelay', $event)"
              />
              <InputGroupAddon> MS </InputGroupAddon>
            </InputGroup>
          </Config>

          <Config title="Slider orientation" desc="Set slider orientation as you need.">
            <SelectButton
              :model-value="metadata.reversedDirection"
              :options="ORIENTATION"
              size="small"
              optionLabel="name"
              option-value="value"
              @update:model-value="globalStore.updateMetadata('reversedDirection', $event)"
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

          <Config title="Stop on last slide" desc="Enable/Disable autoplay stop or not on last slide.">
            <ToggleButton
              :model-value="metadata.stopOnLastSlide"
              onLabel="On"
              offLabel="Off"
              @update:model-value="globalStore.updateMetadata('stopOnLastSlide', $event)"
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
            <div class="flex flex-col items-start gap-2 text-gray-500">
              <label>Active color:</label>

              <ColorPicker
                :model-value="metadata.navigationActiveColor"
                name="color"
                @update:model-value="globalStore.updateMetadata('navigationActiveColor', $event)"
              />
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
            <div class="flex flex-col items-start gap-2 text-gray-500">
              <label>Active color:</label>

              <ColorPicker
                :model-value="metadata.paginationActiveColor"
                name="color"
                @update:model-value="globalStore.updateMetadata('paginationActiveColor', $event)"
              />
            </div>
          </Config>

          <Config title="clickable" desc="Enable/disable pagination clickable.">
            <ToggleButton
              :model-value="metadata.clickable"
              onLabel="On"
              offLabel="Off"
              @update:model-value="globalStore.updateMetadata('clickable', $event)"
            />
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

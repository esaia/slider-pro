<script setup lang="ts">
import { ALL_DIRECTIONS } from "@/constants/constants";
import Config from "@components/UI/Config.vue";
import ToggleButton from "primevue/togglebutton";
import InputText from "primevue/inputtext";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import ColorPicker from "primevue/colorpicker";

import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
</script>
<template>
  <Tabs value="2">
    <TabList>
      <Tab value="0">Autoplay</Tab>
      <Tab value="1">Navigation</Tab>
      <Tab value="2">Pagination</Tab>
    </TabList>
    <TabPanels>
      <TabPanel value="0">
        <div class="space-y-6">
          <Config title="AutoPlay" desc="Select a slide transition effect.">
            <ToggleButton onLabel="On" offLabel="Off" />
          </Config>

          <Config title="Slider speed" desc="Set autoplay scroll speed in millisecond.">
            <InputGroup>
              <InputText type="number" class="!w-24" />
              <InputGroupAddon> MS </InputGroupAddon>
            </InputGroup>
          </Config>

          <Config title="Pause on Hover" desc="Enable/Disable carousel pause on hover.">
            <ToggleButton onLabel="On" offLabel="Off" />
          </Config>
        </div>
      </TabPanel>
      <TabPanel value="1"> navigation </TabPanel>
      <TabPanel value="2">
        <div class="space-y-6">
          <Config title="Enable pagination" desc="Show slider pagination.">
            <ToggleButton onLabel="On" offLabel="Off" />
          </Config>

          <Config title="Pagination Color" desc="Set color for the slider pagination.">
            <div class="flex items-center gap-4">
              <div class="flex flex-col items-start gap-2 text-gray-500">
                <label> Color:</label>
                <ColorPicker name="color" />
              </div>
              <div class="flex flex-col items-start gap-2 text-gray-500">
                <label>Active color:</label>

                <ColorPicker name="color" />
              </div>
            </div>
          </Config>

          <Config title="Enable progress">
            <ToggleButton onLabel="On" offLabel="Off" />
          </Config>

          <Config title="Margin" desc="Set margin for carousel pagination.">
            <div class="flex items-center gap-2">
              <InputGroup v-for="item in ALL_DIRECTIONS" :key="item">
                <InputGroupAddon> {{ item }} </InputGroupAddon>
                <InputText type="number" class="!w-20" />
              </InputGroup>
            </div>
          </Config>
        </div>
      </TabPanel>
    </TabPanels>
  </Tabs>
</template>

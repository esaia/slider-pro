<script setup lang="ts">
import Tabs from "primevue/tabs";
import TabList from "primevue/tablist";
import Tab from "primevue/tab";
import TabPanels from "primevue/tabpanels";
import TabPanel from "primevue/tabpanel";
import GeneralSettings from "../settingsTabs/GeneralSettings.vue";
</script>

<template>
  <div class="w-full">
    <Tabs value="0">
      <TabList>
        <Tab value="0">General settings</Tab>
        <Tab value="1">Style settings</Tab>
        <Tab value="2">other</Tab>
      </TabList>

      <TabPanels>
        <TabPanel value="0">
          <GeneralSettings />
        </TabPanel>
        <TabPanel value="1">
          <p class="m-0">123123</p>
        </TabPanel>
        <TabPanel value="2">
          <p class="m-0">123123</p>
        </TabPanel>
      </TabPanels>
    </Tabs>
  </div>
</template>

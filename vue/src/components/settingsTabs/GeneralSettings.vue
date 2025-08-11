<script setup lang="ts">
import { CLICK_ACTIONS, DEVICES, DIRECTIONS, ORDER_BY_OPTIONS, ORIENTATION, TRANSITIONS } from "@/constants/constants";
import Config from "@components/UI/Config.vue";
import Select from "primevue/select";
import InputText from "primevue/inputtext";
import ToggleButton from "primevue/togglebutton";

import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";

import SelectButton from "primevue/selectbutton";
</script>
<template>
  <div class="space-y-6">
    <Config title="Slide Effect" desc="Select a slide transition effect.">
      <Select
        :options="TRANSITIONS"
        optionLabel="name"
        placeholder="Select a transition"
        size="small"
        class="w-full md:w-56"
      />
    </Config>

    <Config title="Columns" desc="Set number of column on devices.">
      <div class="flex items-center gap-2">
        <InputGroup v-for="item in DEVICES" :key="item">
          <InputGroupAddon> {{ item }} </InputGroupAddon>
          <InputText type="number" class="!w-20" />
        </InputGroup>
      </div>
    </Config>

    <Config title="Space between" desc="Set a space between the items.">
      <InputGroup>
        <InputText type="number" class="!w-20" />
        <InputGroupAddon> PX </InputGroupAddon>
      </InputGroup>
    </Config>

    <Config title="Click Action Type" desc="Set a click action type for the items.">
      <SelectButton :options="CLICK_ACTIONS" size="small" optionLabel="name" />
    </Config>

    <Config title="Order by" desc="Set an order by option.">
      <SelectButton :options="ORDER_BY_OPTIONS" size="small" optionLabel="name" />
    </Config>

    <Config title="Infinite Loop" desc="Enable/Disable infinite loop mode.">
      <ToggleButton onLabel="On" offLabel="Off" />
    </Config>

    <Config title="Carousel Direction" desc="Set carousel direction as you need.">
      <SelectButton :options="DIRECTIONS" size="small" optionLabel="name" />
    </Config>

    <Config title="Carousel Orientation" desc="Choose a slider orientation.">
      <SelectButton :options="ORIENTATION" size="small" optionLabel="name" />
    </Config>
  </div>
</template>

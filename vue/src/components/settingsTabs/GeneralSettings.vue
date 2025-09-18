<script setup lang="ts">
import { CLICK_ACTIONS, DEVICES, DIRECTIONS, ORDER_BY_OPTIONS, TRANSITIONS } from "@/constants/constants";
import Config from "@components/UI/Config.vue";
import Select from "primevue/select";
import InputText from "primevue/inputtext";
import ToggleButton from "primevue/togglebutton";
import InputGroup from "primevue/inputgroup";
import InputGroupAddon from "primevue/inputgroupaddon";
import SelectButton from "primevue/selectbutton";

import { useGlobalStore } from "@/store/useGlobal";
import { storeToRefs } from "pinia";

const globalStore = useGlobalStore();
const { metadata } = storeToRefs(globalStore);
</script>
<template>
  <div class="space-y-6">
    <Config title="Slide Effect" desc="Select a slide transition effect.">
      <Select
        :model-value="metadata.slideEffect"
        :options="TRANSITIONS"
        option-value="value"
        optionLabel="name"
        placeholder="Select a transition"
        size="small"
        class="w-56"
        @update:model-value="globalStore.updateMetadata('slideEffect', $event)"
      />
    </Config>

    <Config title="Columns" desc="Set number of column on devices.">
      <div class="flex items-center gap-2">
        <InputGroup v-for="device in DEVICES" :key="device">
          <InputGroupAddon> {{ device }} </InputGroupAddon>
          <InputText
            :model-value="metadata.columns[device].toString()"
            type="number"
            step="0.1"
            class="!w-20"
            @update:model-value="globalStore.updateMetadata(`columns.${device}`, $event)"
          />
        </InputGroup>
      </div>
    </Config>

    <Config title="Padding top" desc="Set a padding top % in range 1-100.">
      <InputGroup>
        <InputText
          :model-value="metadata.paddingTop.toString()"
          type="number"
          class="!w-20"
          @update:model-value="globalStore.updateMetadata('paddingTop', Number($event))"
        />
        <InputGroupAddon> % </InputGroupAddon>
      </InputGroup>
    </Config>

    <Config title="Space between" desc="Set a space between the items.">
      <InputGroup>
        <InputText
          :model-value="metadata.spaceBetween.toString()"
          type="number"
          class="!w-20"
          @update:model-value="globalStore.updateMetadata('spaceBetween', Number($event))"
        />
        <InputGroupAddon> PX </InputGroupAddon>
      </InputGroup>
    </Config>

    <Config title="Click Action Type" desc="Set a click action type for the items.">
      <SelectButton
        :model-value="metadata.clickAction"
        :options="CLICK_ACTIONS"
        size="small"
        optionLabel="name"
        option-value="value"
        :invalid="!metadata.clickAction"
        @update:model-value="globalStore.updateMetadata('clickAction', $event)"
      />
    </Config>

    <Config title="Order by" desc="Set an order by option.">
      <SelectButton
        :model-value="metadata.orderBy"
        :options="ORDER_BY_OPTIONS"
        size="small"
        optionLabel="name"
        option-value="value"
        @update:model-value="globalStore.updateMetadata('orderBy', $event)"
      />
    </Config>

    <Config title="Infinite Loop" desc="Enable/Disable infinite loop mode.">
      <ToggleButton
        :model-value="metadata.infiniteLoop"
        onLabel="On"
        offLabel="Off"
        @update:model-value="globalStore.updateMetadata('infiniteLoop', $event)"
      />
    </Config>

    <Config title="Slider direction" desc="Choose a slider direction.">
      <SelectButton
        :model-value="metadata.sliderDirection"
        :options="DIRECTIONS"
        size="small"
        optionLabel="name"
        option-value="value"
        @update:model-value="globalStore.updateMetadata('sliderDirection', $event)"
      />
    </Config>

    <Config title="Center Slides" desc="Enable/Disable center slide mode.">
      <ToggleButton
        :model-value="metadata.centerSlides"
        onLabel="On"
        offLabel="Off"
        @update:model-value="globalStore.updateMetadata('centerSlides', $event)"
      />
    </Config>
  </div>
</template>

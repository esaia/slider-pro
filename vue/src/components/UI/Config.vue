<script setup lang="ts">
defineProps<{
  title: string;
  desc: string;
}>();
</script>
<template>
  <div class="flex gap-8">
    <div class="w-[200px]">
      <div class="font-semibold text-black">{{ title }}</div>
      <div class="text-gray-500 italic">{{ desc }}</div>
    </div>
    <div>
      <slot />
    </div>
  </div>
</template>

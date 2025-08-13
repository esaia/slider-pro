import { ComponentCustomProperties } from "vue";

declare module "@vue/runtime-core" {
  interface ComponentCustomProperties {
    sliderPro: {
      nonce: string;
      ajax_url: string;
      plugin_url: string;
      plugin_assets_path: string;
    };
  }
}

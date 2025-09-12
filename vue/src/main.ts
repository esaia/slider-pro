import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import { createPinia } from "pinia";
import Noir from "./presets/Noir.ts";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import SliderShortcode from "./shortcode/SliderShortcode.vue";

const app = createApp(App);
const pinia = createPinia();

app.use(PrimeVue, {
  theme: {
    preset: Noir,
    options: {
      prefix: "p",
      darkModeSelector: ".p-dark"
    }
  }
});

app.use(pinia);
app.use(ToastService);

app.config.globalProperties.sliderPro = sliderPro;

app.mount("#slider-pro-vue-app");

document.body.querySelectorAll("[id^='slider-pro-shortcode-']").forEach((shortcodeElement) => {
  if (shortcodeElement.tagName === "SCRIPT") return;

  const sliderId = shortcodeElement.getAttribute("data-slider-id");
  console.log("sliderId", sliderId);

  const shortcodeFlatsApp = createApp(SliderShortcode, { sliderId });

  shortcodeFlatsApp.mount(shortcodeElement);
});

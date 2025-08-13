import { createApp } from "vue";
import "./style.css";
import App from "./App.vue";
import { createPinia } from "pinia";
import Noir from "./presets/Noir.ts";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";

const app = createApp(App);
const pinia = createPinia();

app.use(PrimeVue, {
  theme: {
    preset: Noir,
    options: {
      prefix: "p",
      darkModeSelector: ".p-dark",
      cssLayer: false
    }
  }
});

app.use(pinia);
app.use(ToastService);

app.config.globalProperties.sliderPro = sliderPro;

app.mount("#slider-pro-vue-app");

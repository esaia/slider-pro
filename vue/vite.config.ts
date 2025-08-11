import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import { resolve } from "path";

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(), tailwindcss()],

  resolve: {
    alias: {
      "@": resolve(__dirname, "./src/"),
      "@components": resolve(__dirname, "./src/components/")
    }
  },

  build: {
    outDir: "../dist/",
    emptyOutDir: true,
    chunkSizeWarningLimit: 1600,

    rollupOptions: {
      output: {
        format: "es",
        entryFileNames: `assets/[name].js`,
        chunkFileNames: `assets/[name].js`,
        assetFileNames: `assets/[name].[ext]`
      }
    }
  }
});

import { defineConfig } from "vite";

export default defineConfig(async () => {
  const vue = (await import("@vitejs/plugin-vue")).default;
  const tailwind = (await import("@tailwindcss/vite")).default;
  

  return {
    plugins: [
      vue(),
      tailwind()
    ],
  };
});
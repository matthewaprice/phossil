import { rmSync, writeFileSync } from "node:fs"
import { resolve } from "node:path"
import tailwindcss from "@tailwindcss/vite"
import { defineConfig } from "vite"

const app = resolve(import.meta.dirname, "phossil")

// phossil's components, outside the app folder Vite watches by default.
const src = resolve(import.meta.dirname, "src")

// Where the browser reaches the dev server. It runs in the vite container,
// so it can't work this out for itself.
const origin = "http://localhost:5173"

// Writes phossil/hot while the dev server runs, so PHP links the page to it
// instead of to the built files.
function hotFile() {
  const file = resolve(app, "hot")
  const remove = () => rmSync(file, { force: true })

  return {
    name: "hot-file",
    configureServer(server) {
      server.httpServer?.once("listening", () => writeFileSync(file, origin))
      process.on("exit", remove)
      for (const signal of ["SIGINT", "SIGTERM", "SIGHUP"]) {
        process.on(signal, () => process.exit())
      }
    },
  }
}

// Reloads the page when a PHP template changes, since Vite can't hot-swap HTML
// that PHP renders. Invalidating first makes Tailwind scan the templates again,
// which it otherwise misses for a file in a new folder, such as a new view.
function reloadOnPhp() {
  return {
    name: "reload-on-php",
    configureServer(server) {
      server.watcher.add(src)
      const reload = (file) => {
        if (file.endsWith(".php")) {
          server.environments.client.moduleGraph.invalidateAll()
          server.ws.send({ type: "full-reload", path: "*" })
        }
      }
      server.watcher.on("change", reload)
      server.watcher.on("add", reload)
      server.watcher.on("unlink", reload)
    },
  }
}

export default defineConfig({
  root: app,
  plugins: [tailwindcss(), hotFile(), reloadOnPhp()],
  server: {
    host: true,
    port: 5173,
    strictPort: true,
    origin,
    watch: {
      ignored: ["**/dist/**"],
    },
  },
  build: {
    // phossil/dist/assets/, served at /assets/ beside the images. The build copies
    // it into dist/, and PHP reads the manifest to link the hashed files.
    outDir: "dist",
    emptyOutDir: true,
    manifest: true,
    rolldownOptions: {
      input: ["css/app.css", "js/app.js"].map((entry) => resolve(app, entry)),
    },
  },
})

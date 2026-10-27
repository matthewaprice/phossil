#!/bin/sh
# Installs the node tools inside the container, so its Linux binaries never land in
# the host's node_modules, then starts the Vite dev server.
cd /srv
CI=true pnpm install --frozen-lockfile
# exec, so Vite receives the stop signal and removes phossil/hot as it exits.
exec node_modules/.bin/vite

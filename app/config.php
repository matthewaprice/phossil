<?php

return [
    // Shows PHP errors in the page.
    'debug' => true,

    // Composer's vendor directory, next to app/.
    'vendor' => dirname(__DIR__) . '/vendor',

    // URL our img/ is served under. nginx and the static build both use this
    // path, so every environment matches. Vite's CSS and JS go to /assets/ too.
    'assets' => '/assets',
];

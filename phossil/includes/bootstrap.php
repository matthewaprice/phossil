<?php
// Loads the app without dispatching a request, for index.php and the static build.

use Phossil\Phossil;

$config = require dirname(__DIR__) . '/config.php';

// ini_set('display_errors', $config['debug'] ? 1 : 0);
// ini_set('display_startup_errors', $config['debug'] ? 1 : 0);
// ini_set('log_errors', 1);
// error_reporting(E_ALL);

// Also loads phossil's helpers: e(), asset() and vite().
require $config['vendor'] . '/autoload.php';

// The phossil directory: phossil reads img/, Vite's hot file and dist/ from it.
Phossil::boot(dirname(__DIR__), $config);

require dirname(__DIR__) . '/routes/web.php';

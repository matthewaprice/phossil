<?php
// Loads the app without dispatching a request, for index.php and the static build.

global $app, $routes;

$app = new stdClass();
// The phossil directory. Every other path is built from it.
$app->root = dirname(__DIR__);
$app->config = require $app->root . '/config.php';

// ini_set('display_errors', $app->config['debug'] ? 1 : 0);
// ini_set('display_startup_errors', $app->config['debug'] ? 1 : 0);
// ini_set('log_errors', 1);
// error_reporting(E_ALL);

require $app->config['vendor'] . '/autoload.php';

require $app->root . '/includes/functions.php';

require $app->root . '/routes/web.php';

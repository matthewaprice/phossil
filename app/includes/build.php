<?php

use App\Services\Build;

require_once __DIR__ . '/bootstrap.php';

$build = new Build();
$build->routes();
$build->notFound();
$build->images();
$build->vite();

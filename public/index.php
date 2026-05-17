<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Remove stale bootstrap cache entries for classes that no longer exist (e.g. dev-only packages removed via --no-dev)
(static function () {
    foreach (['packages', 'services'] as $cache) {
        $path = __DIR__ . '/../bootstrap/cache/' . $cache . '.php';
        if (!file_exists($path)) {
            continue;
        }
        $data = include $path;
        if (!is_array($data)) {
            continue;
        }
        $dirty = false;
        foreach (['providers', 'eager', 'deferred'] as $key) {
            if (!isset($data[$key])) {
                continue;
            }
            $filtered = array_values(array_filter((array) $data[$key], fn($v) => class_exists(is_array($v) ? $v[0] ?? $v : $v)));
            if (count($filtered) !== count($data[$key])) {
                $data[$key] = $filtered;
                $dirty = true;
            }
        }
        if ($dirty) {
            @file_put_contents($path, '<?php return ' . var_export($data, true) . ';' . PHP_EOL);
        }
    }
})();

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());

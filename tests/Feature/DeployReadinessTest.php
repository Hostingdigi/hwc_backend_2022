<?php

use Illuminate\Support\Facades\Route;

// Things `php artisan optimize` (config/route caching) needs on a production
// deploy. Each of these was broken before Phase 3.

it('gives every named route a unique name (route:cache fails otherwise)', function () {
    // A bare group prefix like "admin." is how Laravel marks an unnamed route in a
    // named group; route:cache treats those as unnamed, so they are skipped here.
    $names = collect(Route::getRoutes())->map->getName()->filter()->reject(fn ($name) => str_ends_with($name, '.'));

    expect($names->duplicates()->values()->all())->toBe([]);
});

it('points every route at a controller method that exists', function () {
    $missing = collect(Route::getRoutes())
        ->map->getActionName()
        ->filter(fn ($action) => str_contains($action, '@'))
        ->reject(function ($action) {
            [$class, $method] = explode('@', $action);
            return method_exists($class, $method);
        })
        ->values()->all();

    expect($missing)->toBe([]);
});

it('finds every view the code names, with the exact file-name case (Linux is case-sensitive)', function () {
    $tracked = [];
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('views')));
    foreach ($files as $file) {
        if (str_ends_with($file, '.blade.php')) {
            $rel = str_replace('\\', '/', substr($file, strlen(resource_path('views')) + 1, -strlen('.blade.php')));
            $tracked[$rel] = true;
        }
    }

    $missing = [];
    foreach (['app', 'resources/views'] as $dir) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($dir)));
        foreach ($files as $file) {
            if (!str_ends_with($file, '.php')) continue;
            $code = preg_replace('#^\s*//.*$#m', '', file_get_contents($file)); // skip commented-out lines
            preg_match_all('/(?:\bview|@include|@extends)\(\s*[\'"]([A-Za-z0-9_\/.\-]+)[\'"]/', $code, $m);
            foreach ($m[1] as $name) {
                if (!isset($tracked[str_replace('.', '/', $name)])) {
                    $missing[] = $name . ' (' . basename($file) . ')';
                }
            }
        }
    }

    expect(array_values(array_unique($missing)))->toBe([]);
});

it('reads no env() outside config files (it returns null after config:cache)', function () {
    $offenders = [];
    foreach (['app', 'resources/views', 'routes'] as $dir) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path($dir)));
        foreach ($files as $file) {
            if (!str_ends_with($file, '.php')) continue;
            foreach (file($file) as $n => $line) {
                if (preg_match('#^\s*(\*|/\*)#', $line)) continue; // docblock / comment line
                $code = preg_replace('#//.*|\{\{--.*--\}\}#', '', $line);
                if (preg_match('/(?<![\w>:])env\(/', $code)) {
                    $offenders[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file) . ':' . ($n + 1);
                }
            }
        }
    }

    expect($offenders)->toBe([]);
});

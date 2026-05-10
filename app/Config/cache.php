<?php

use App\Lib\Cache\FileCache;

function appCache(): FileCache
{
    static $cache = null;

    if ($cache instanceof FileCache) {
        return $cache;
    }

    // Saltar verificación del directorio si el caché está explícitamente deshabilitado.
    if (!filter_var(env('CACHE_ENABLED', true), FILTER_VALIDATE_BOOLEAN)) {
        $cache = new FileCache('', false);
        return $cache;
    }

    $cachePath = __DIR__ . '/../../storage/cache';
    if (!is_dir($cachePath)) {
        if (!mkdir($cachePath, 0777, true) && !is_dir($cachePath)) {
            trigger_error('Unable to create cache directory: ' . $cachePath, E_USER_WARNING);
            $cache = new FileCache($cachePath, false);
            return $cache;
        }
    }

    if (!is_writable($cachePath)) {
        @chmod($cachePath, 0777);
    }

    if (!is_writable($cachePath)) {
        trigger_error('Cache directory is not writable: ' . $cachePath, E_USER_WARNING);
        $cache = new FileCache($cachePath, false);
        return $cache;
    }

    $cache = new FileCache(
        $cachePath,
        (bool) env('CACHE_ENABLED', true)
    );

    return $cache;
}

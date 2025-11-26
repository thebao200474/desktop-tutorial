<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = $_SESSION['csrf_token'];
}

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/config/config.php';

if (!defined('APP_BASE_URL') || !defined('ASSET_BASE_URL')) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = str_replace('\\', '/', (string) dirname($scriptName));
    if ($scriptDir === '.' || $scriptDir === '/') {
        $scriptDir = '';
    }
    $scriptDir = rtrim($scriptDir, '/');

    $servingFromPublic = $scriptDir !== '' && substr($scriptDir, -7) === '/public';
    $appBase = $servingFromPublic ? substr($scriptDir, 0, -7) : $scriptDir;
    if ($appBase === false) {
        $appBase = '';
    }

    $appBase = $appBase === '/' ? '' : $appBase;
    $assetBase = $servingFromPublic
        ? $scriptDir
        : ($appBase === '' ? '' : $appBase . '/public');

    if (!defined('APP_BASE_URL')) {
        $normalisedBase = $appBase === '' ? '' : '/' . ltrim($appBase, '/');
        define('APP_BASE_URL', $normalisedBase);
    }

    if (!defined('ASSET_BASE_URL')) {
        if ($assetBase === '' || $assetBase === '/') {
            define('ASSET_BASE_URL', '');
        } else {
            define('ASSET_BASE_URL', '/' . ltrim($assetBase, '/'));
        }
    }
}

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        $base = APP_BASE_URL;
        $base = $base === '/' ? '' : rtrim($base, '/');
        $path = ltrim($path, '/');

        if ($path === '') {
            return $base === '' ? '/' : $base;
        }

        return ($base === '' ? '' : $base) . '/' . $path;
    }
}

if (!function_exists('asset_url')) {
    function asset_url(string $path): string
    {
        $base = ASSET_BASE_URL;
        $base = $base === '/' ? '' : rtrim($base, '/');
        $path = ltrim($path, '/');

        return ($base === '' ? '' : $base) . '/' . $path;
    }
}

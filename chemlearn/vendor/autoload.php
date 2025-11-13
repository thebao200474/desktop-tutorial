<?php
// Autoload thủ công đơn giản để map namespace tới đường dẫn tương ứng.

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'ChemLearn\\Controllers\\' => __DIR__ . '/../controllers/',
        'ChemLearn\\Models\\' => __DIR__ . '/../models/',
        'Bramus\\Router\\' => __DIR__ . '/Bramus/Router/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($class, $prefix, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
});

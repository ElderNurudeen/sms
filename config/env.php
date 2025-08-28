<?php
require_once __DIR__ . '/../lib/helpers.php';

/**
 * Load .env variables into $_ENV
 */
function loadEnv($path = __DIR__ . '/../.env')
{
    if (!file_exists($path)) {
        jsonError(".env file not found at $path", 500);
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0)
            continue; // skip comments

        list($name, $value) = explode("=", $line, 2);
        $name = trim($name);
        $value = trim($value);

        $_ENV[$name] = $value;
        putenv("$name=$value");
    }
}

<?php
/** Vercel entry point (vercel-php runtime). Every non-asset request is routed here by vercel.json. */
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        echo '<pre>TEMP DEBUG: ' . htmlspecialchars($e['message'] . ' @ ' . $e['file'] . ':' . $e['line']) . '</pre>';
    }
});
require __DIR__ . '/../public/index.php';

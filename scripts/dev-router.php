<?php
/**
 * Local development only:  php -S 127.0.0.1:8080 -t public scripts/dev-router.php
 * then open http://127.0.0.1:8080/arabia/
 * Serves assets with the right MIME types and gzip (like Apache mod_deflate does in production).
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$gzip = str_contains($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip');
if (str_starts_with($path, '/arabia/assets/')) {
    $file = __DIR__ . '/../public' . substr($path, strlen('/arabia'));
    if (is_file($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $types = ['css' => 'text/css', 'js' => 'application/javascript', 'webp' => 'image/webp', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'svg' => 'image/svg+xml',
            'avif' => 'image/avif', 'ico' => 'image/x-icon', 'mp4' => 'video/mp4', 'webm' => 'video/webm', 'woff2' => 'font/woff2'];
        header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
        header('Cache-Control: public, max-age=31536000, immutable');
        $data = file_get_contents($file);
        if ($gzip && in_array($ext, ['css', 'js', 'svg'], true)) {
            header('Content-Encoding: gzip');
            header('Vary: Accept-Encoding');
            $data = gzencode($data, 6);
        }
        header('Content-Length: ' . strlen($data));
        echo $data;
        return true;
    }
}
if ($path === '/') { header('Location: /arabia/'); return true; }
if ($gzip && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') ob_start('ob_gzhandler');
require __DIR__ . '/../public/index.php';

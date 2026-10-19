<?php
/** Front controller. Every request under /arabia/ that isn't a static file lands here (see .htaccess / nginx.conf.example). */
require __DIR__ . '/../app/bootstrap.php';

dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');

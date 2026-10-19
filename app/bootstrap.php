<?php
declare(strict_types=1);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Riyadh');

define('APP_DIR', __DIR__);
define('ROOT_DIR', dirname(__DIR__));

$GLOBALS['config'] = require APP_DIR . '/config.php';

require APP_DIR . '/lib/helpers.php';
require APP_DIR . '/lib/db.php';
require APP_DIR . '/lib/i18n.php';
require APP_DIR . '/lib/routes.php';
require APP_DIR . '/lib/seo.php';
require APP_DIR . '/lib/schema.php';
require APP_DIR . '/lib/render.php';
require APP_DIR . '/lib/forms.php';
require APP_DIR . '/lib/mailer.php';
require APP_DIR . '/lib/assessment.php';
require APP_DIR . '/lib/controller.php';

if (!is_staging()) {
    ini_set('display_errors', '0');
}

<?php

/** UI string lookup from app/lang/{en,ar}.php. Dot keys supported: t('form.full_name'). */
function t(string $key, ?string $lang = null)
{
    static $strings = [];
    $lang = $lang ?? lang();
    if (!isset($strings[$lang])) $strings[$lang] = require APP_DIR . "/lang/{$lang}.php";
    $v = $strings[$lang];
    foreach (explode('.', $key) as $k) {
        if (!is_array($v) || !array_key_exists($k, $v)) return $key;
        $v = $v[$k];
    }
    return $v;
}

function other_lang(): string
{
    return is_ar() ? 'en' : 'ar';
}

function og_locale(string $lang): string
{
    return $lang === 'ar' ? 'ar_SA' : 'en_SA';
}

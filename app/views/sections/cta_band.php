<?php
/** cta_band (B5): the CTA band is rendered at the top of the footer (one CTA, no repeated band). This passes the page's own text there, if it has one; 'hide' => true drops it when the page already ends with its own CTA. */
if (!empty($s['text'])) $GLOBALS['footer_cta'] = $s['text'];
if (!empty($s['hide'])) $GLOBALS['footer_cta_hide'] = true;

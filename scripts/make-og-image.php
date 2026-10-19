<?php
/**
 * Builds public/assets/img/og-default.jpg (1200×630) for Open Graph / Twitter cards:
 * DWP aurora background + white logo + headline.  Run: php scripts/make-og-image.php
 */
$root = dirname(__DIR__) . '/public/assets';
$W = 1200; $H = 630;
$im = imagecreatetruecolor($W, $H);
imagefill($im, 0, 0, imagecolorallocate($im, 4, 7, 13));
$bg = @imagecreatefromwebp("$root/img/hero/hero-aurora-poster.webp");
if ($bg) {
    $bw = imagesx($bg); $bh = imagesy($bg);
    $scale = max($W / $bw, $H / $bh);
    imagecopyresampled($im, $bg, (int) (($W - $bw * $scale) / 2), (int) (($H - $bh * $scale) / 2), 0, 0, (int) ($bw * $scale), (int) ($bh * $scale), $bw, $bh);
}
// darken for legibility
imagefilledrectangle($im, 0, 0, $W, $H, imagecolorallocatealpha($im, 2, 4, 8, 50));
$logo = @imagecreatefrompng("$root/img/logo-light.png");
if ($logo) {
    $lh = 96; $lw = (int) (imagesx($logo) * $lh / imagesy($logo));
    imagealphablending($im, true);
    imagecopyresampled($im, $logo, 80, 80, 0, 0, $lw, $lh, imagesx($logo), imagesy($logo));
}
$ttf = null;
foreach (['/System/Library/Fonts/Supplemental/Arial Bold.ttf', '/Library/Fonts/Arial Bold.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf'] as $f) if (is_file($f)) { $ttf = $f; break; }
$white = imagecolorallocate($im, 255, 255, 255);
$soft = imagecolorallocate($im, 190, 205, 222);
$blue = imagecolorallocate($im, 143, 188, 240);
if ($ttf) {
    imagettftext($im, 50, 0, 80, 330, $white, $ttf, 'SAP Business One & AI');
    imagettftext($im, 50, 0, 80, 400, $blue, $ttf, 'for Growing Saudi Companies');
    imagettftext($im, 24, 0, 80, 520, $soft, $ttf, 'Certified team in Riyadh  ·  ZATCA-ready ERP  ·  dwptech.com/arabia');
}
imagejpeg($im, "$root/img/og-default.jpg", 85);
echo "Wrote img/og-default.jpg (" . round(filesize("$root/img/og-default.jpg") / 1024) . " KB)\n";

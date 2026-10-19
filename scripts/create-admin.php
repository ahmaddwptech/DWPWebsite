<?php
/** Create or reset an admin user:  php scripts/create-admin.php you@dwptech.com  (prompts for a password) */
require __DIR__ . '/../app/bootstrap.php';
if (PHP_SAPI !== 'cli') exit(1);
$email = strtolower(trim($argv[1] ?? ''));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { fwrite(STDERR, "Usage: php scripts/create-admin.php email@example.com\n"); exit(1); }
fwrite(STDOUT, 'Password (min 12 chars): ');
system('stty -echo 2>/dev/null');
$pass = trim((string) fgets(STDIN));
system('stty echo 2>/dev/null');
fwrite(STDOUT, "\n");
if (mb_strlen($pass) < 12) { fwrite(STDERR, "Password too short.\n"); exit(1); }
$hash = password_hash($pass, PASSWORD_DEFAULT);
db()->prepare('INSERT INTO admin_users (email, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)')->execute([$email, $hash]);
echo "Admin user saved: $email\n";

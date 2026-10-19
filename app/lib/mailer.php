<?php
/**
 * Plain-text email. Uses SMTP (STARTTLS/SSL + AUTH LOGIN) when smtp.host is set, otherwise PHP mail().
 */
function send_mail(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $from = cfg('mail_from') ?: ('no-reply@' . parse_url(cfg('base_url'), PHP_URL_HOST));
    $encSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers = [
        'From: DWP Arabia Website <' . $from . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: base64',
    ];
    if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) $headers[] = 'Reply-To: ' . $replyTo;
    $encBody = chunk_split(base64_encode($body));

    $smtp = cfg('smtp');
    try {
        if (empty($smtp['host'])) {
            $ok = mail($to, $encSubject, $encBody, implode("\r\n", $headers));
        } else {
            $ok = smtp_send($smtp, $from, $to, array_merge(['To: ' . $to, 'Subject: ' . $encSubject, 'Date: ' . date('r'), 'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . parse_url(cfg('base_url'), PHP_URL_HOST) . '>'], $headers), $encBody);
        }
    } catch (Throwable $e) {
        log_line('mail.log', $e->getMessage());
        $ok = false;
    }
    if (!$ok) log_line('mail.log', "Failed to send '$subject' to $to");
    return $ok;
}

function smtp_send(array $c, string $from, string $to, array $headers, string $body): bool
{
    $secure = $c['secure'] ?? 'tls';
    $host = ($secure === 'ssl' ? 'ssl://' : '') . $c['host'];
    $fp = @stream_socket_client($host . ':' . (int) $c['port'], $errno, $errstr, 10);
    if (!$fp) throw new RuntimeException("SMTP connect failed: $errstr");
    stream_set_timeout($fp, 10);
    $read = function () use ($fp) {
        $data = '';
        while (($line = fgets($fp, 515)) !== false) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $data;
    };
    $cmd = function (string $c, array $expect) use ($fp, $read) {
        fwrite($fp, $c . "\r\n");
        $r = $read();
        if (!in_array((int) substr($r, 0, 3), $expect, true)) throw new RuntimeException('SMTP error after ' . strtok($c, ' ') . ': ' . trim($r));
        return $r;
    };
    $read();
    $ehlo = 'EHLO ' . (parse_url(cfg('base_url'), PHP_URL_HOST) ?: 'localhost');
    $cmd($ehlo, [250]);
    if ($secure === 'tls') {
        $cmd('STARTTLS', [220]);
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) throw new RuntimeException('STARTTLS failed');
        $cmd($ehlo, [250]);
    }
    if (!empty($c['user'])) {
        $cmd('AUTH LOGIN', [334]);
        $cmd(base64_encode($c['user']), [334]);
        $cmd(base64_encode($c['pass']), [235]);
    }
    $cmd('MAIL FROM:<' . $from . '>', [250]);
    $cmd('RCPT TO:<' . $to . '>', [250, 251]);
    $cmd('DATA', [354]);
    $cmd(implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.", [250]);
    $cmd('QUIT', [221]);
    fclose($fp);
    return true;
}

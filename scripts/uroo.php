<?php

/**
 * composer uroo — stack dev (serve + queue + pail + vite) + tunnel ngrok.
 *
 * URL ngrok dari proses "ngrok" dipakai app mobile untuk sinkronisasi
 * (diisi di layar login / pengaturan HP).
 *
 * Domain tetap (opsional): UROO_NGROK_DOMAIN=xxx.ngrok-free.app composer uroo
 * Pratinjau perintah saja:  UROO_DRY_RUN=1 composer uroo
 */
$stack = [
    'server' => 'php artisan serve',
    'queue' => 'php artisan queue:listen --tries=1 --timeout=0',
    'logs' => 'php artisan pail --timeout=0',
    'vite' => 'npm run dev',
];

$palette = ['#93c5fd', '#c4b5fd', '#fb7185', '#fdba74'];

$hasNgrok = trim((string) shell_exec('command -v ngrok 2>/dev/null')) !== '';

if ($hasNgrok) {
    $domain = (string) getenv('UROO_NGROK_DOMAIN');
    $stack['ngrok'] = 'ngrok http http://127.0.0.1:8000'.($domain !== '' ? " --domain={$domain}" : '');
    $palette[] = '#67e8f9';
} else {
    fwrite(STDERR, "\n\033[33m== ngrok tidak ditemukan di PATH — menjalankan stack tanpa tunnel ==\033[0m\n");
    fwrite(STDERR, "Install dari https://ngrok.com/download lalu jalankan:\n");
    fwrite(STDERR, "  ngrok config add-authtoken <TOKEN>\n\n");
}

$names = implode(',', array_keys($stack));
$colors = implode(',', $palette);
$procs = implode(' ', array_map(fn ($cmd) => escapeshellarg($cmd), $stack));

$cmd = "npx concurrently --kill-others --names={$names} -c ".escapeshellarg($colors)." {$procs}";

if (getenv('UROO_DRY_RUN')) {
    echo $cmd.PHP_EOL;

    exit(0);
}

passthru($cmd, $code);

exit($code);

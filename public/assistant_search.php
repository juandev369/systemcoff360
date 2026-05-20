<?php
header('Content-Type: application/json; charset=utf-8');

$q = trim($_REQUEST['q'] ?? '');
if ($q === '') {
    echo json_encode(['ok' => false, 'error' => 'Empty query']);
    exit;
}

$root = dirname(__DIR__);
$dirs = [
    $root . '/Documentacion',
    $root . '/views',
    $root . '/controllers',
    $root . '/models',
    $root . '/config'
];

$exts = ['md','php','txt','sql','html'];
$matches = [];
$limit = 12;

foreach ($dirs as $d) {
    if (!is_dir($d)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($d));
    foreach ($it as $file) {
        if (!$file->isFile()) continue;
        $path = $file->getPathname();
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), $exts)) continue;
        // small file safeguard
        $size = $file->getSize();
        if ($size === 0 || $size > 2 * 1024 * 1024) continue;
        $content = file_get_contents($path);
        if ($content === false) continue;
        $pos = mb_stripos($content, $q);
        if ($pos !== false) {
            // build snippet (extract surrounding chars and sanitize)
            $start = max(0, $pos - 120);
            $snippet = mb_substr($content, $start, 300);
            $snippet = preg_replace('/\s+/', ' ', $snippet);
            $matches[] = [
                'file' => str_replace($root . '/', '', $path),
                'snippet' => $snippet
            ];
            if (count($matches) >= $limit) break 2;
        }
    }
}

if (empty($matches)) {
    echo json_encode(['ok' => true, 'matches' => []]);
} else {
    echo json_encode(['ok' => true, 'matches' => $matches]);
}

// EOF

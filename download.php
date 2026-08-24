<?php

// Set the base URL that should be stripped
$baseUrl = 'https://demo.themesurf.com/plumbing/';

$urls = [
    $baseUrl . 'image/gallery/g1-lg.jpg',
    $baseUrl . 'image/gallery/g2-lg.jpg',
    $baseUrl . 'image/gallery/g3-lg.jpg',
    $baseUrl . 'image/gallery/g4-lg.jpg',
    $baseUrl . 'image/gallery/g5-lg.jpg',
    $baseUrl . 'image/gallery/g6-lg.jpg',
    $baseUrl . 'image/gallery/g7-lg.jpg',
    $baseUrl . 'image/gallery/g8-lg.jpg',
    $baseUrl . 'image/gallery/g9-lg.jpg',
    $baseUrl . 'image/gallery/g10-lg.jpg',
];

$downloadDir = __DIR__ . '';

foreach ($urls as $url) {
    // 1. Remove base URL prefix to isolate relative local path
    $relativePath = str_replace($baseUrl, '', $url);
    $relativePath = ltrim($relativePath, '/');

    // 2. Resolve destination path and target directory
    $destinationPath = $downloadDir . '/' . $relativePath;
    $directory = dirname($destinationPath);

    // 3. Create non-existent directory paths automatically
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    // 4. Download file using cURL
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_ENCODING       => '', // Automatic gzip/deflate decoding
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER     => [
            'Accept: text/html,application/xhtml+xml,application/xml,text/css,image/*,*/*;q=0.8',
            'Referer: ' . $baseUrl
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    ]);

    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // 5. Save file on successful download
    if ($httpCode === 200 && $content !== false) {
        file_put_contents($destinationPath, $content);
        echo "Successfully downloaded: {$relativePath}\n";
    } else {
        echo "Failed [HTTP {$httpCode}]: {$url} (Error: {$curlError})\n";
    }
}

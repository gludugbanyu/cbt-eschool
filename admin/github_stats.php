<?php

$repo = "gludugbanyu/cbt-eschool";
$cache_file = __DIR__ . "/github_cache.json";
$cache_time = 1800; // 30 menit

// Jika cache ada dan belum expired
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    header("Content-Type: application/json");
    echo file_get_contents($cache_file);
    exit;
}

// Ambil dari GitHub
$url = "https://api.github.com/repos/$repo";

$options = [
    "http" => [
        "header" => "User-Agent: CBT-ESchool\r\n"
    ]
];

$context = stream_context_create($options);
$response = @file_get_contents($url, false, $context);

if ($response) {
    $data = json_decode($response, true);

    $result = [
        "stars" => $data['stargazers_count'] ?? 0,
        "forks" => $data['forks_count'] ?? 0,
        "updated" => date("Y-m-d H:i:s")
    ];

    file_put_contents($cache_file, json_encode($result));

    header("Content-Type: application/json");
    echo json_encode($result);
} else {
    // fallback kalau gagal
    if (file_exists($cache_file)) {
        header("Content-Type: application/json");
        echo file_get_contents($cache_file);
    } else {
        echo json_encode(["stars" => 0, "forks" => 0]);
    }
}
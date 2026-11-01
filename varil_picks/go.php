
<?php

require_once __DIR__ . '/config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

$stmt = $pdo->prepare(
    "SELECT affiliate_url FROM products WHERE id = ? LIMIT 1"
);

$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    http_response_code(404);
    exit('Produk tidak ditemukan.');
}

$url = trim($product['affiliate_url']);
$scheme = parse_url($url, PHP_URL_SCHEME);

if (
    !filter_var($url, FILTER_VALIDATE_URL) ||
    !in_array(strtolower($scheme ?? ''), ['http', 'https'], true)
) {
    http_response_code(400);
    exit('Link affiliate tidak valid.');
}

header('Location: ' . $url, true, 302);
exit;


<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Pastikan permintaan menggunakan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

// Periksa token keamanan
$token = $_POST['csrf_token'] ?? '';

if (
    empty($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $token)
) {
    exit('Token keamanan tidak cocok. Silakan kembali ke dashboard dan coba lagi.');
}

// Periksa ID produk
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('ID produk tidak valid.');
}

try {
    // Cari produk
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        exit('Produk tidak ditemukan.');
    }

    // Hapus produk dari database
    $delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $delete->execute([$id]);

    if ($delete->rowCount() !== 1) {
        exit('Produk gagal dihapus dari database.');
    }

    // Hapus foto jika berasal dari folder uploads
    $image = $product['image_url'] ?? '';

    if (preg_match('/\Auploads\/[a-f0-9]{32}\.(jpg|jpeg|png|webp)\z/i', $image)) {
        $uploadDir = realpath(__DIR__ . '/../uploads');
        $imagePath = realpath(__DIR__ . '/../' . $image);

        if (
            $uploadDir &&
            $imagePath &&
            dirname($imagePath) === $uploadDir &&
            is_file($imagePath)
        ) {
            unlink($imagePath);
        }
    }

    header('Location: dashboard.php');
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    exit('Gagal menghapus produk. Error database: ' . htmlspecialchars($e->getMessage()));
}

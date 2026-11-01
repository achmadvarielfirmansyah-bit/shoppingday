
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

// Token keamanan
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// PROSES HAPUS PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        exit('Token keamanan tidak valid. Silakan refresh halaman.');
    }

    $id = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        exit('ID produk tidak valid.');
    }

    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        exit('Produk tidak ditemukan.');
    }

    $delete = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $delete->execute([$id]);

    if ($delete->rowCount() !== 1) {
        exit('Produk gagal dihapus.');
    }

    // Hapus foto produk dari folder uploads
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

    header('Location: dashboard.php?deleted=1');
    exit;
}

// Ambil data produk
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - VARIL PICKS</title>

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    padding: 25px;
    background: #0b0b12;
    color: white;
    font-family: Arial, sans-serif;
}

.container {
    max-width: 1000px;
    margin: auto;
}

.stats, .product {
    background: #171725;
    border: 1px solid #292940;
    border-radius: 12px;
    padding: 18px;
    margin-bottom: 15px;
}

.stats h2 {
    color: #a78bfa;
    font-size: 32px;
    margin: 5px 0;
}

.btn {
    display: inline-block;
    padding: 12px 16px;
    background: #6d4aff;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    margin: 0 5px 20px 0;
}

.product {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.product img {
    width: 85px;
    height: 85px;
    object-fit: cover;
    border-radius: 8px;
}

.product-info {
    flex: 1;
    min-width: 180px;
}

.number {
    color: #a78bfa;
    font-weight: bold;
    margin-bottom: 8px;
}

.product-info p {
    color: #999;
    font-size: 13px;
}

.delete-form {
    width: 100%;
    margin-top: 10px;
}

.delete-btn {
    width: 100%;
    padding: 13px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

.delete-btn:hover {
    background: #dc2626;
}

.success {
    background: #14532d;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 15px;
}

.empty {
    color: #aaa;
}

@media (min-width: 600px) {
    .delete-form {
        width: 150px;
    }
}
</style>
</head>

<body>
<div class="container">

    <h1>VARIL PICKS</h1>
    <p style="color:#999;">
        Selamat datang, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>!
    </p>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="success">Produk berhasil dihapus!</div>
    <?php endif; ?>

    <div class="stats">
        <p>Total Produk</p>
        <h2><?= (int)$totalProducts ?></h2>
    </div>

    <a href="add-product.php" class="btn">+ Tambah Produk</a>
    <a href="../index.php" class="btn">Lihat Website</a>
    <a href="logout.php" class="btn">Logout</a>

    <h2>Daftar Semua Produk</h2>

    <?php if (!empty($products)): ?>

        <?php foreach ($products as $product): ?>
            <div class="product">

                <?php if (!empty($product['image_url'])): ?>
                    <img
                        src="../<?= htmlspecialchars($product['image_url']) ?>"
                        alt="Foto produk"
                    >
                <?php endif; ?>

                <div class="product-info">
                    <div class="number">
                        #<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?>
                    </div>

                    <strong><?= htmlspecialchars($product['name']) ?></strong>

                    <p><?= htmlspecialchars($product['created_at']) ?></p>
                </div>

                <form
                    class="delete-form"
                    method="POST"
                    action="dashboard.php"
                    onsubmit="return confirm('Yakin ingin menghapus produk ini?')"
                >
                    <input
                        type="hidden"
                        name="delete_id"
                        value="<?= (int)$product['id'] ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                    >

                    <button type="submit" class="delete-btn">
                        HAPUS PRODUK
                    </button>
                </form>

            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <p class="empty">Belum ada produk.</p>
    <?php endif; ?>

</div>
</body>
</html>

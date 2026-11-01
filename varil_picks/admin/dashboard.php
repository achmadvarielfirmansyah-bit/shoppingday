
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$totalProducts = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | VARIL PICKS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard {
            max-width: 1100px;
            margin: 50px auto;
            padding: 20px;
        }

        .welcome {
            margin-bottom: 30px;
        }

        .welcome p {
            color: #9298a8;
            margin-top: 8px;
        }

        .stat-card {
            background: #11141e;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .stat-card h2 {
            font-size: 36px;
            color: #a78bfa;
            margin-top: 10px;
        }

        .admin-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .admin-actions a {
            padding: 13px 20px;
            background: #a78bfa;
            color: #080a10;
            border-radius: 10px;
            font-weight: bold;
            font-size: 13px;
        }

        .admin-actions a.logout {
            background: #292d3a;
            color: white;
        }

        .product-list {
            background: #11141e;
            border-radius: 15px;
            padding: 20px;
            overflow-x: auto;
        }

        .product-list table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .product-list th,
        .product-list td {
            padding: 14px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            font-size: 13px;
        }

        .product-list th {
            color: #a78bfa;
        }

        .product-list td {
            color: #ddd;
        }
    </style>
</head>
<body>

<div class="dashboard">

    <div class="welcome">
        <h1>Dashboard VARIL PICKS</h1>
        <p>Selamat datang, <?= htmlspecialchars($_SESSION['admin_username']) ?>!</p>
    </div>

    <div class="stat-card">
        <p>Total Produk</p>
        <h2><?= $totalProducts ?></h2>
    </div>

    <div class="admin-actions">
        <a href="add-product.php">+ Tambah Produk</a>
        <a href="../index.php" target="_blank">Lihat Website</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="product-list">
        <h3 style="margin-bottom:15px;">Produk Terbaru</h3>

        <table>
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Nama Produk</th>
                    <th>Waktu Ditambahkan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products): ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td>#<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Belum ada produk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>

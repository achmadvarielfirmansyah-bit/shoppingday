
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../config/db.php';

// Hitung total produk
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Ambil semua produk
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
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            background: #0b0b12;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            margin-bottom: 5px;
        }

        .subtitle {
            color: #999;
            margin-bottom: 25px;
        }

        .stats {
            background: #171725;
            border: 1px solid #292940;
            padding: 22px;
            border-radius: 14px;
            margin-bottom: 25px;
        }

        .stats h2 {
            margin: 0;
            font-size: 32px;
            color: #a78bfa;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 9px;
            text-decoration: none;
            color: white;
            background: #6d4aff;
            font-size: 14px;
        }

        .btn.secondary {
            background: #292940;
        }

        .table-wrap {
            overflow-x: auto;
            background: #171725;
            border: 1px solid #292940;
            border-radius: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #292940;
        }

        th {
            color: #b9a8ff;
            background: #202033;
        }

        td {
            color: #ddd;
        }

        .product-image {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
        }

        .delete-btn {
            background: #ef4444;
            color: white;
            border: 0;
            padding: 9px 13px;
            border-radius: 7px;
            cursor: pointer;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .empty {
            text-align: center;
            color: #999;
            padding: 30px;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>VARIL PICKS</h1>
    <p class="subtitle">Dashboard Admin</p>

    <div class="stats">
        <p>Total Produk</p>
        <h2><?= (int)$totalProducts ?></h2>
    </div>

    <div class="actions">
        <a href="add-product.php" class="btn">+ Tambah Produk</a>
        <a href="../index.php" class="btn secondary">Lihat Website</a>
        <a href="logout.php" class="btn secondary">Logout</a>
    </div>

    <h2>Daftar Semua Produk</h2>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Waktu Ditambahkan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (count($products) > 0): ?>

                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>#<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?></td>

                            <td>
                                <?php if (!empty($product['image_url'])): ?>
                                    <img
                                        src="../<?= htmlspecialchars($product['image_url']) ?>"
                                        class="product-image"
                                        alt="Foto produk"
                                    >
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($product['name']) ?></td>

                            <td><?= htmlspecialchars($product['created_at']) ?></td>

                            <td>
                                <form
                                    action="delete-product.php"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus produk ini?')"
                                >
                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$product['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                                    >

                                    <button type="submit" class="delete-btn">
                                        HAPUS
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty">
                            Belum ada produk.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>

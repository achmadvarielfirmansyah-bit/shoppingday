
<?php
require_once 'config/db.php';

$stmt = $pdo->query("
    SELECT * FROM products
    ORDER BY id DESC
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>VARIL PICKS | Curated Finds</title>

    <meta name="description"
    content="Temukan koleksi produk pilihan VARIL PICKS dan beli langsung melalui Shopee.">

    <link rel="stylesheet" href="assets/CSS/style.css">
    
</head>


<body>

<header class="navbar">
    <a href="index.php" class="logo">
        VARIL<span>.</span>PICKS
    </a>

    <a href="#products" class="nav-button">
        Lihat Koleksi <span>↗</span>
    </a>
</header>

<section class="hero">
    <div class="hero-content">

        <span class="tag">✦ CURATED DAILY FINDS</span>

        <h1>
            Temukan Produk<br>
            <span>Favoritmu.</span>
        </h1>

        <p>
            Koleksi produk pilihan yang kami temukan untukmu.
            Lihat nomor produk dari video dan temukan produknya di sini.
        </p>

        <a href="#products" class="hero-button">
            Jelajahi Produk <span>↓</span>
        </a>

    </div>

    <div class="hero-decoration">
        <span>V.</span>
    </div>
</section>

<main id="products" class="container">

    <div class="section-heading">
        <div>
            <span class="subtitle">OUR COLLECTION</span>
            <h2>Produk Pilihan</h2>
            <p>Temukan produk berdasarkan nomor yang ada di video.</p>
        </div>

        <div class="total">
            <?= count($products) ?> PRODUK
        </div>
    </div>

    <?php if (!empty($products)): ?>

        <div class="product-grid">

            <?php foreach ($products as $product): ?>

                <article class="product-card">

                    <div class="product-image">

                        <?php if (!empty($product['image_url'])): ?>

                            <img
                                src="<?= htmlspecialchars($product['image_url']) ?>"
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                loading="lazy"
                            >

                        <?php else: ?>

                            <div class="no-image">
                                VARIL PICKS
                            </div>

                        <?php endif; ?>

                        <span class="product-number">
                            #<?= str_pad($product['id'], 3, '0', STR_PAD_LEFT) ?>
                        </span>

                    </div>

                    <div class="product-info">

                        <span class="product-label">
                            VARIL PICKS COLLECTION
                        </span>

                        <h3>
                            <?= htmlspecialchars($product['name']) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars(
                                $product['description'] ?? 'Produk pilihan VARIL PICKS.'
                            ) ?>
                        </p>

                        <a
                            href="go.php?id=<?= (int)$product['id'] ?>"
                            class="product-button"
                        >
                            Lihat Produk di Shopee
                            <span>↗</span>
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty-state">
            <div class="empty-icon">✦</div>
            <h3>Belum Ada Produk</h3>
            <p>Produk pilihan akan segera hadir di sini.</p>
        </div>

    <?php endif; ?>

</main>

<footer class="footer">

    <h3>VARIL<span>.</span>PICKS</h3>

    <p>Simple finds, better choices.</p>

    <small>
        © <?= date('Y') ?> VARIL PICKS. All rights reserved.
    </small>

</footer>
<a href="admin/login.php" class="admin-hidden">Admin</a>
</body>
</html>

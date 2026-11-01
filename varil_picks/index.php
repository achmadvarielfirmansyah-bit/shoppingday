
<?php
require_once 'config/db.php';

// Mengambil kata pencarian
$search = trim($_GET['q'] ?? '');

// Mengambil produk dari database
if ($search !== '') {
    $keyword = ltrim($search, '#');

    $stmt = $pdo->prepare("
        SELECT * FROM products
        WHERE CAST(id AS CHAR) LIKE ?
        OR name LIKE ?
        ORDER BY id DESC
    ");

    $stmt->execute([
        '%' . $keyword . '%',
        '%' . $search . '%'
    ]);

    $products = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("
        SELECT * FROM products
        ORDER BY id DESC
    ");

    $products = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>VARIL PICKS | Find Your Product</title>

    <meta name="description"
    content="Temukan produk dari video VARIL PICKS dan langsung menuju Shopee.">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<!-- NAVBAR -->
<header class="navbar">
    <a href="index.php" class="logo">
        VARIL<span>.</span>PICKS
    </a>

    <a href="#products" class="nav-button">
        EXPLORE ↗
    </a>
</header>


<!-- HERO SECTION -->
<section class="hero">

    <div class="hero-content">

        <div class="tag">
            ✦ YOUR DAILY PRODUCT FINDS
        </div>

        <h1>
            FIND IT.<br>
            <span>GET IT.</span>
        </h1>

        <p>
            Lihat produk yang kamu temukan di video.
            Cari nomor produknya dan langsung temukan
            link Shopee-nya di sini.
        </p>

        <a href="#products" class="hero-button">
            EXPLORE PRODUCTS ↓
        </a>

    </div>

    <div class="hero-glow"></div>

</section>


<!-- PRODUCT SECTION -->
<main id="products" class="container">

    <div class="section-heading">

        <div>
            <span class="subtitle">OUR COLLECTION</span>

            <h2>Product Directory</h2>

            <p>
                Temukan produk berdasarkan nomor di video.
            </p>
        </div>

        <div class="total">
            <?= count($products) ?> ITEMS
        </div>

    </div>


    <!-- SEARCH -->
    <form method="GET" class="search-box">

        <span>⌕</span>

        <input
            type="text"
            name="q"
            placeholder="Cari nomor atau nama produk..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <button type="submit">
            SEARCH
        </button>

    </form>


    <!-- PRODUCT LIST -->
    <?php if (count($products) > 0): ?>

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
                            VIDEO PRODUCT
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
                            LIHAT DI SHOPEE
                            <span>↗</span>
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="empty-state">

            <div class="empty-icon">✦</div>

            <h3>Produk belum ditemukan</h3>

            <p>
                <?= $search !== ''
                    ? 'Coba cari menggunakan nomor atau nama produk lain.'
                    : 'Produk baru akan muncul di sini setelah ditambahkan admin.'
                ?>
            </p>

        </div>

    <?php endif; ?>

</main>


<!-- FOOTER -->
<footer class="footer">

    <h3>VARIL<span>.</span>PICKS</h3>

    <p>
        Discover your next favorite product.
    </p>

    <small>
        © <?= date('Y') ?> VARIL PICKS. All rights reserved.
    </small>

</footer>

</body>
</html>

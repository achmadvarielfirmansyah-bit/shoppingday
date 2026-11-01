
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $affiliate_url = trim($_POST['affiliate_url'] ?? '');

    if ($name === '' || $affiliate_url === '') {
        $error = 'Nama produk dan link affiliate wajib diisi!';
    } elseif (
        !filter_var($affiliate_url, FILTER_VALIDATE_URL) ||
        !in_array(parse_url($affiliate_url, PHP_URL_SCHEME), ['http', 'https'], true)
    ) {
        $error = 'Link affiliate tidak valid!';
    } elseif (
        !isset($_FILES['image']) ||
        $_FILES['image']['error'] !== UPLOAD_ERR_OK
    ) {
        $error = 'Silakan pilih gambar produk terlebih dahulu!';
    } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        $error = 'Ukuran gambar maksimal 5 MB!';
    } else {
        $tmpFile = $_FILES['image']['tmp_name'];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmpFile);

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowedTypes[$mime]) || !getimagesize($tmpFile)) {
            $error = 'Format gambar harus JPG, PNG, atau WEBP!';
        } else {
            $uploadDir = __DIR__ . '/../uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $filename = bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mime];
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($tmpFile, $destination)) {
                $imagePath = 'uploads/' . $filename;

                try {
                    $stmt = $pdo->prepare(
                        "INSERT INTO products
                        (name, description, image_url, affiliate_url)
                        VALUES (?, ?, ?, ?)"
                    );

                    $stmt->execute([
                        $name,
                        $description,
                        $imagePath,
                        $affiliate_url
                    ]);

                    $newId = $pdo->lastInsertId();

                    $success = 'Produk berhasil ditambahkan! Nomor produk: #' .
                        str_pad($newId, 3, '0', STR_PAD_LEFT);

                } catch (PDOException $e) {
                    if (file_exists($destination)) {
                        unlink($destination);
                    }

                    $error = 'Produk gagal disimpan ke database!';
                }
            } else {
                $error = 'Gagal mengunggah gambar!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk | VARIL PICKS</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .form-container {
            max-width: 650px;
            margin: 50px auto;
            padding: 25px;
        }

        .form-box {
            background: #11141e;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 18px;
            padding: 30px;
        }

        .form-box h1 {
            margin-bottom: 10px;
        }

        .form-box > p {
            color: #9298a8;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .form-box label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: bold;
        }

        .form-box input,
        .form-box textarea {
            width: 100%;
            padding: 14px;
            margin-bottom: 20px;
            background: #191e2b;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            color: white;
            font-family: inherit;
        }

        .form-box textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-box input[type="file"] {
            cursor: pointer;
        }

        .preview {
            display: none;
            width: 100%;
            max-height: 250px;
            object-fit: contain;
            background: #191e2b;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .form-box button {
            width: 100%;
            padding: 15px;
            background: #a78bfa;
            color: #080a10;
            border: none;
            border-radius: 10px;
            font-weight: 800;
            cursor: pointer;
        }

        .message {
            padding: 13px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .success {
            background: #123b2c;
            color: #8df0bd;
        }

        .error {
            background: #401c25;
            color: #ff9aaa;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #a78bfa;
        }
    </style>
</head>

<body>

<div class="form-container">

    <a href="dashboard.php" class="back-link">
        ← Kembali ke Dashboard
    </a>

    <div class="form-box">

        <h1>Tambah Produk</h1>
        <p>Masukkan informasi produk Shopee Affiliate kamu.</p>

        <?php if ($success): ?>
            <div class="message success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Nama Produk *</label>
            <input type="text" name="name"
                   placeholder="Contoh: Hoodie Oversized"
                   maxlength="255" required>

            <label>Deskripsi Produk</label>
            <textarea name="description"
                      placeholder="Tuliskan deskripsi produk..."></textarea>

            <label>Foto Produk *</label>
            <input type="file" name="image"
                   accept="image/jpeg,image/png,image/webp"
                   id="imageInput" required>

            <img id="preview" class="preview" alt="Preview gambar produk">

            <label>Link Shopee Affiliate *</label>
            <input type="url" name="affiliate_url"
                   placeholder="https://s.shopee.co.id/..."
                   required>

            <button type="submit">SIMPAN PRODUK</button>

        </form>

    </div>
</div>

<script>
const imageInput = document.getElementById('imageInput');
const preview = document.getElementById('preview');

imageInput.addEventListener('change', function () {
    const file = this.files[0];

    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
});
</script>

</body>
</html>

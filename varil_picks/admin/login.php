
<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];

        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | VARIL PICKS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 35px;
            background: #11141e;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 20px;
        }

        .login-box h1 {
            font-size: 27px;
            margin-bottom: 8px;
        }

        .login-box p {
            color: #9298a8;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .login-box input {
            width: 100%;
            padding: 14px;
            margin-bottom: 15px;
            background: #191e2b;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            color: white;
            outline: none;
        }

        .login-box button {
            width: 100%;
            padding: 14px;
            background: #a78bfa;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }

        .error {
            color: #ff7777;
            font-size: 13px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h1>VARIL <span style="color:#a78bfa">PICKS</span></h1>
    <p>Masuk ke panel administrator</p>

    <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username"
               placeholder="Username" required>

        <input type="password" name="password"
               placeholder="Password" required>

        <button type="submit">LOGIN ADMIN</button>
    </form>
</div>

</body>
</html>

<?php
session_start();
// Jika sudah login, redirect sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
        exit();
    } elseif ($_SESSION['role'] === 'paramedis') {
        header('Location: paramedis/dashboard.php');
        exit();
    }
}

// Tampilkan error jika ada
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login RSCH</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #0a2e50;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background-color: #0a2e50;
            border: 1px solid #a4f1dc;
            padding: 2rem 3rem 3rem;
            border-radius: 10px;
            width: 360px;
            box-shadow: 0 0 10px #a4f1dc;
            text-align: center;
        }
        .login-box img {
            max-width: 150px;
            margin-bottom: 1.5rem;
        }
        h2 {
            color: #a4f1dc;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 0.4rem 0 1rem 0;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
        }
        input[type="submit"] {
            background-color: #a4f1dc;
            border: none;
            padding: 12px 0;
            width: 50%;
            border-radius: 25px;
            color: #0a2e50;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 0 auto; /* Center the button */
            display: block;
        }
        input[type="submit"]:hover {
            background-color: #72d6c9;
        }
        .error {
            background-color: #dc3545;
            padding: 0.5rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-box">
    <img src="logo-rsch.png" alt="Logo RSCH" />
    <h2>Login RSCH</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="proses_login.php" method="post" autocomplete="off">
        <input type="text" name="username" placeholder="Username" required autofocus />
        <input type="password" name="password" placeholder="Password" required />
        <input type="submit" value="Masuk" />
    </form>
</div>

</body>
</html>

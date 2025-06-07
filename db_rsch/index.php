<!-- File: index.php -->
<?php 
session_start(); ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login - RSCH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background: #0a2647; /* biru tua RSCH */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background:rgb(87, 151, 181); /* hijau lembut RSCH */
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
        }
        .logo {
            width: 120px;
            margin-bottom: 25px;
            filter: drop-shadow(1px 1px 1px rgba(0,0,0,0.25));
        }
        label {
            color: #0a2647; /* teks biru tua */
            font-weight: 600;
        }
        .form-control {
            border-radius: 10px;
            border: 1.5px solid #0a2647;
            padding: 10px;
            font-size: 1rem;
        }
        .btn-primary {
            background-color: #0a2647;
            border-color: #0a2647;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 0;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #06304f;
            border-color: #06304f;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .login-wrapper {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container text-center">
            <img src="logo-rsch.png" alt="Logo RSCH" class="logo" />
            <h3 class="mb-4" style="color:#0a2647;">Login RSCH</h3>
            <form action="proses_login.php" method="POST">
                <div class="form-group text-start">
                    <label for="username">Username</label>
                    <input id="username" type="text" name="username" class="form-control" required />
                </div>
                <div class="form-group text-start">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" class="form-control" required />
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>

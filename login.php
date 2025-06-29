<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Karyawan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: url('https://www.familydocs.org/wp-content/uploads/2019/11/coffee-shop-1149155_1920.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-container {
            background: rgba(255,255,255,0.92);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18);
            padding: 38px 32px 28px 32px;
            width: 350px;
            text-align: center;
            backdrop-filter: blur(2px);
            margin: 40px auto; 
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .input-group {
            position: relative;
            margin-bottom: 18px;
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .input-group input {
            width: 100%;
            max-width: 300px;
            padding: 12px 16px 12px 42px;
            border: none;
            border-radius: 24px;
            background: #f5f6fa;
            font-size: 1rem;
            box-shadow: 0 1px 2px rgba(44,62,80,0.04);
            outline: none;
            transition: box-shadow 0.2s;
            margin: 0 auto;
            display: block;
        }
        .input-group .fa {
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1.1rem;
            pointer-events: none;
        }
        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 16px;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(44,62,80,0.12);
        }
        .welcome {
            font-size: 1.3rem;
            font-weight: 600;
            color: #222;
            margin-bottom: 22px;
        }
        .input-group {
            position: relative;
            margin-bottom: 18px;
        }
        .input-group input {
            width: 100%;
            padding: 12px 16px 12px 42px;
            border: none;
            border-radius: 24px;
            background: #f5f6fa;
            font-size: 1rem;
            box-shadow: 0 1px 2px rgba(44,62,80,0.04);
            outline: none;
            transition: box-shadow 0.2s;
        }
        .input-group input:focus {
            box-shadow: 0 2px 8px rgba(44,62,80,0.10);
            background: #fff;
        }
        .input-group .fa {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1.1rem;
        }
        .login-btn {
            width: 100%;
            padding: 12px 0;
            background: #444;
            color: #fff;
            border: none;
            border-radius: 24px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            letter-spacing: 1px;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background: #222;
        }
        .forgot-link, .create-link {
            display: block;
            margin-top: 16px;
            color: #222;
            text-decoration: none;
            font-size: 0.97rem;
            opacity: 0.85;
            transition: color 0.2s;
        }
        .forgot-link:hover, .create-link:hover {
            color: #4e54c8;
        }
        .create-link {
            margin-top: 10px;
            font-size: 1rem;
            font-weight: 500;
        }
        .error-msg {
            color: #e74c3c;
            margin-bottom: 10px;
            font-size: 0.98rem;
        }
    </style>
</head>
<body>
    <form class="login-container" action="proses_login.php" method="POST">
        <img src="https://png.pngtree.com/recommend-works/png-clipart/20240827/ourmid/pngtree-simple-coffee-shop-logo-png-image_13624083.png" alt="avatar" class="avatar">
        <div class="welcome">Kayu Kopi</div>
        <?php if (isset($_SESSION['error'])) { echo "<div class='error-msg'>" . $_SESSION['error'] . "</div>"; unset($_SESSION['error']); } ?>
        <div class="input-group">
            <i class="fa fa-user"></i>
            <input type="text" name="username" placeholder="Username or Email" required autocomplete="username">
        </div>
        <div class="input-group">
            <i class="fa fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
        </div>
        <button type="submit" class="login-btn">LOGIN</button>
    </form>
</body>
</html>
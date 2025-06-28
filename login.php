<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Karyawan</title>
</head>
<body>
    <h2>Login Karyawan</h2>
    <form action="proses_login.php" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($_SESSION['error'])) { echo "<p style='color:red'>" . $_SESSION['error'] . "</p>"; unset($_SESSION['error']); } ?>
</body>
</html>
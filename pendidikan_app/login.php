<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'app/config/db.php';
    $u = $_POST['username'];
    $p = $_POST['password'];
    $q = $conn->query("SELECT * FROM users WHERE username='$u' AND password='$p'");
    if ($q->num_rows > 0) {
        $_SESSION['login'] = true;
        header("Location: index.php");
    } else {
        echo "Login gagal.";
    }
}
?>
<form method="post">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
<?php
session_start();
if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
// ... (sisa logika PHP login)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require 'app/config/db.php';
    $u = $_POST['username'];
    $p = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Login gagal. Username atau password salah.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="flex w-full max-w-4xl bg-white rounded-lg shadow-lg overflow-hidden mx-4">
            <div class="hidden md:block md:w-1/2">
                <img src="kampusundira.jpg" alt="Foto Kampus" class="object-cover w-full h-full">
            </div>

            <div class="w-full p-8 md:w-1/2">
                <h2 class="text-2xl font-bold text-center text-gray-700 mb-6"><i class="fas fa-lock text-blue-500"></i> Silakan Login</h2>
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $error ?></span>
                    </div>
                <?php endif; ?>
                <form method="post" class="space-y-4">
                    <div>
                        <label for="username" class="text-sm font-bold text-gray-600 block">Username</label>
                        <input id="username" class="w-full mt-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="text" name="username" required>
                    </div>
                    <div>
                        <label for="password" class="text-sm font-bold text-gray-600 block">Password</label>
                        <input id="password" class="w-full mt-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" type="password" name="password" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
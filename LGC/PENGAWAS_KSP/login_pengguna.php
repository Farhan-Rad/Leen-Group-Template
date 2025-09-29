<?php
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Contoh validasi sederhana, ganti dengan validasi database Anda
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Contoh user pengguna
    if ($username === 'pengguna' && $password === 'password123') {
        $_SESSION['user_role'] = 'pengguna';
        $_SESSION['username'] = $username;
        header('Location: pengguna_ksp_index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login Pengguna - KSP Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center text-emerald-600">Login Pengguna</h1>
        <?php if ($error): ?>
            <p class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="login_pengguna.php" class="space-y-4">
            <div>
                <label for="username" class="block mb-1 font-medium text-slate-700">Username</label>
                <input type="text" id="username" name="username" required class="w-full p-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div>
                <label for="password" class="block mb-1 font-medium text-slate-700">Password</label>
                <input type="password" id="password" name="password" required class="w-full p-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold transition-colors">Login</button>
        </form>
    </div>
</body>
</html>
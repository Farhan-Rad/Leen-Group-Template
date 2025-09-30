<?php
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Contoh validasi sederhana, ganti dengan validasi database Anda
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Contoh user pengawas
    if ($username === 'pengawas' && $password === 'password123') {
        $_SESSION['user_role'] = 'pengawas';
        $_SESSION['username'] = $username;
        header('Location: pengawas_ksp_index.php');
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
    <title>Login Pengawas - KSP Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center text-emerald-600">Login Pengawas</h1>
        <?php if ($error): ?>
            <p class="bg-red-100 text-red-700 p-3 rounded mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="login_pengawas.php" class="space-y-4">
            <div>
                <label for="username" class="block mb-1 font-medium text-slate-700">Username</label>
                <input type="text" id="username" name="username" required class="w-full p-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>
            <div class="relative">
                <label for="password" class="block mb-1 font-medium text-slate-700">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    class="w-full p-2 border border-slate-300 rounded focus:outline-none focus:ring-2 focus:ring-emerald-500 pr-10" 
                />
                <button 
                    type="button" 
                    id="togglePassword" 
                    class="absolute right-3 bottom-3"
                    aria-label="Toggle password visibility"
                    >

<!-- Ikon mata tertutup (default) -->
                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-4.477-10-10a9.96 9.96 0 012.175-6.125M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18" />
                    </svg>
                    <!-- Ikon mata terbuka (disembunyikan default) -->
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded font-semibold transition-colors">Login</button>
        </form>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeClosed = document.querySelector('#eyeClosed');
        const eyeOpen = document.querySelector('#eyeOpen');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'password') {
                eyeClosed.classList.remove('hidden');
                eyeOpen.classList.add('hidden');
            } else {
                eyeClosed.classList.add('hidden');
                eyeOpen.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>

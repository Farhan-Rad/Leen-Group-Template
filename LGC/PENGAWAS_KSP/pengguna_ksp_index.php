<?php
session_start();

$kirimMessage = '';
if (isset($_SESSION['kirimMessage'])) {
    $kirimMessage = $_SESSION['kirimMessage'];
    unset($_SESSION['kirimMessage']);
}

// Proses Upload Kirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToKirim"]) && isset($_POST['submit'])) {
    $target_dir = "../uploads/hasil_kirim_ksp/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES["fileToKirim"]["name"]);
    $uploadOk = 1;

    if ($_FILES["fileToKirim"]["error"] !== UPLOAD_ERR_OK) {
        $kirimMessage = "<p class='bg-red-100 text-red-700 p-4 rounded-md mb-4'>Terjadi kesalahan saat mengirim file.</p>";
        $uploadOk = 0;
    }

    if ($uploadOk && move_uploaded_file($_FILES["fileToKirim"]["tmp_name"], $target_file)) {
        $kirimMessage = "<p class='bg-emerald-100 text-emerald-700 p-4 rounded-md mb-4'>File " . htmlspecialchars(basename($_FILES["fileToKirim"]["name"])) . " berhasil diupload.</p>";
    } elseif ($uploadOk) {
        $kirimMessage = "<p class='bg-red-100 text-red-700 p-4 rounded-md mb-4'>Maaf, file gagal di kirim.</p>";
    }

    $_SESSION['kirimMessage'] = $kirimMessage;
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// Proses Download Kiriman
$dir_kirim = "../uploads/hasil_kirim_ksp/";
$kirimFiles = [];
$kirimSearch = isset($_GET['search_kirim']) ? strtolower($_GET['search_kirim']) : '';

if (is_dir($dir_kirim)) {
    foreach (scandir($dir_kirim) as $file) {
        $filePath = $dir_kirim . $file;
        if (is_file($filePath) && ($kirimSearch === '' || strpos(strtolower($file), $kirimSearch) !== false)) {
            $kirimFiles[] = $file;
        }
    }
}

$showKirimDownload = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_download_kirim'], $_POST['download_file'])) {
    $selectedFile = basename($_POST['download_file']);
    $selectedPath = $dir_kirim . $selectedFile;
    if (is_file($selectedPath)) {
        $showKirimDownload = "
        <div class='bg-white p-6 rounded-lg shadow-md mt-6'>
            <form method='post'>
                <input type='hidden' name='download_file' value='" . htmlspecialchars($selectedFile, ENT_QUOTES) . "'>
                <button type='submit' name='download' class='bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md font-medium transition-colors'>Unduh " . htmlspecialchars($selectedFile) . "</button>
            </form>
        </div>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'], $_POST['download_file'])) {
    $downloadFile = basename($_POST['download_file']);
    $downloadPath = $dir_kirim . $downloadFile;
    if (is_file($downloadPath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($downloadPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($downloadPath));
        flush();
        readfile($downloadPath);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kirim & Kiriman - KSP Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex flex-col min-h-screen">
    <header class="bg-emerald-500 p-6 shadow-md">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
            <h1 class="text-3xl font-bold text-white mb-4 md:mb-0">KSP Manager</h1>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="about.php" class="text-white hover:text-emerald-100 transition-colors">Tentang</a></li>
                    <li><a href="login_pengguna.php" class="text-white hover:text-emerald-100 transition-colors">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 max-w-4xl flex-grow">
        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-slate-800">Pilih file untuk di Kirim</h2>
            <?php echo $kirimMessage; ?>
            <form action="pengguna_ksp_index.php" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
                <label for="fileToKirim" class="block text-sm font-medium text-slate-700 mb-2">Pilih file untuk di Kirim:</label>
                <input type="file" name="fileToKirim" id="fileToKirim" class="w-full p-2 border border-slate-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                <input type="submit" value="Kirim File" name="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md font-medium transition-colors" />
            </form>
        </section>

        <section>
            <h2 class="text-2xl font-semibold mb-4 text-slate-800">Cari dan Download Kiriman</h2>
            <form class="search bg-white p-4 rounded-lg shadow-md mb-6" method="get" action="pengguna_ksp_index.php">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="text" name="search_kirim" placeholder="Cari file..." value="<?php echo htmlspecialchars($kirimSearch); ?>" class="flex-1 p-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md font-medium transition-colors">Cari</button>
                </div>
            </form>

            <?php if (!empty($kirimFiles)): ?>
                <h2 class="text-xl font-semibold mb-4 text-slate-800">List file kiriman</h2>
                <ul class="space-y-2">
                    <?php foreach ($kirimFiles as $file): ?>
                        <li class="bg-white p-4 rounded-md shadow-sm border-l-4 border-emerald-500">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="download_file" value="<?php echo htmlspecialchars($file, ENT_QUOTES); ?>" />
                                <button type="submit" name="show_download_kirim" class="text-emerald-600 hover:text-emerald-700 font-medium transition-colors w-full text-left"><?php echo htmlspecialchars($file); ?></button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-slate-600">Tidak ada file ditemukan.</p>
            <?php endif; ?>

            <?php echo $showKirimDownload; ?>
        </section>
    </main>

    <footer class="bg-emerald-500 text-white py-6 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 KSP Manager</p>
        </div>
    </footer>
</body>
</html>

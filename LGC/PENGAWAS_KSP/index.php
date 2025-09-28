<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KSP Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800">
    <header class="bg-emerald-500 p-6 shadow-md">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
            <h1 class="text-3xl font-bold text-white mb-4 md:mb-0">KSP Manager</h1>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="about.php" class="text-white hover:text-emerald-100 transition-colors">Tentang</a></li>
                    <li><a href="login.php" class="text-white hover:text-emerald-100 transition-colors">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mx-auto px-4 py-8 max-w-4xl">
        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-slate-800">Upload</h2>
            <form action="index.php" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
                <label for="fileToUpload" class="block text-sm font-medium text-slate-700 mb-2">Pilih file untuk di Upload:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" class="w-full p-2 border border-slate-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <input type="submit" value="Upload File" name="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md font-medium transition-colors">
            </form>
        </section>

        <?php
            session_start();
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
                $target_dir = "../uploads/template_ksp/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
                $uploadOk = 1;
                $uploadMessage = "";

                if ($_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK) {
                    $uploadMessage = "<p class='bg-emerald-100 text-emerald-700 p-4 rounded-md mb-4'>Terjadi kesalahan saat mengupload file.</p>";
                    $uploadOk = 0;
                }

                if ($uploadOk && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $uploadMessage = "<p class='bg-emerald-100 text-emerald-700 p-4 rounded-md mb-4'>File ". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) ." berhasil diupload.</p>";
                } elseif ($uploadOk) {
                    $uploadMessage = "<p class='bg-red-100 text-red-700 p-4 rounded-md mb-4'>Maaf, file gagal diupload.</p>";
                }

                $_SESSION['uploadMessage'] = $uploadMessage;
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }

            if (isset($_SESSION['uploadMessage'])) {
                echo $_SESSION['uploadMessage'];
                unset($_SESSION['uploadMessage']);
            }
        ?>

        <section class="mb-8">
            <h2 class="text-2xl font-semibold mb-4 text-slate-800">Cari dan Download Template</h2>
            <form class="search bg-white p-4 rounded-lg shadow-md mb-6" method="get" action="#">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="text" name="search" placeholder="Cari file..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="flex-1 p-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md font-medium transition-colors">Cari</button>
                </div>
            </form>

            <?php
            $dir = "../uploads/template_ksp/";
            $files = [];
            if (is_dir($dir)) {
                $search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
                foreach (scandir($dir) as $file) {
                    $filePath = $dir . $file;
                    if (is_file($filePath)) {
                        if ($search === '' || strpos(strtolower($file), $search) !== false) {
                            $files[] = $file;
                        }
                    }
                }
            }

            if (!empty($files)) {
                echo "<h2 class='text-xl font-semibold mb-4 text-slate-800'>List file template</h2>";
                echo "<ul class='space-y-2'>";
                foreach ($files as $file) {
                    $fileUrl = $dir . rawurlencode($file);
                    echo "<li class='bg-white p-4 rounded-md shadow-sm border-l-4 border-emerald-500'>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='download_file' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                            <button type='submit' name='show_download' class='text-emerald-600 hover:text-emerald-700 font-medium transition-colors w-full text-left'>".htmlspecialchars($file)."</button>
                        </form>
                    </li>";
                }
                echo "</ul>";
            } else {
                echo "<p class='text-slate-600'>Tidak ada file ditemukan.</p>";
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_download'], $_POST['download_file'])) {
                $selectedFile = basename($_POST['download_file']);
                $selectedPath = $dir . $selectedFile;
                if (is_file($selectedPath)) {
                    echo "<div class='bg-white p-6 rounded-lg shadow-md mt-6'>
                        <form method='post'>
                            <input type='hidden' name='download_file' value='" . htmlspecialchars($selectedFile, ENT_QUOTES) . "'>
                            <button type='submit' name='download' class='bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-md font-medium transition-colors'>Unduh ".htmlspecialchars($selectedFile)."</button>
                        </form>
                    </div>";
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'], $_POST['download_file'])) {
                $downloadFile = basename($_POST['download_file']);
                $downloadPath = $dir . $downloadFile;
                if (is_file($downloadPath)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($downloadPath).'"');
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
        </section>

        <section class="mb-8">
            <form action="index.php" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
                <label for="fileToKirim" class="block text-sm font-medium text-slate-700 mb-2">Pilih file untuk di Kirim:</label>
                <input type="file" name="fileToKirim" id="fileToKirim" class="w-full p-2 border border-slate-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-cyan-400">
                <input type="submit" value="Kirim File" name="submit" class="bg-cyan-400 hover:bg-cyan-500 text-white px-6 py-2 rounded-md font-medium transition-colors">
            </form>
        </section>

        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToKirim"])) {
                $target_dir = "../uploads/hasil_kirim_ksp/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $target_file = $target_dir . basename($_FILES["fileToKirim"]["name"]);
                $uploadOk = 1;
                $uploadMessage = "";

                if ($_FILES["fileToKirim"]["error"] !== UPLOAD_ERR_OK) {
                    $uploadMessage = "<p class='bg-emerald-100 text-emerald-700 p-4 rounded-md mb-4'>Terjadi kesalahan saat mengirim file.</p>";
                    $uploadOk = 0;
                }

                if ($uploadOk && move_uploaded_file($_FILES["fileToKirim"]["tmp_name"], $target_file)) {
                    $uploadMessage = "<p class='bg-emerald-100 text-emerald-700 p-4 rounded-md mb-4'>File ". htmlspecialchars(basename($_FILES["fileToKirim"]["name"])) ." berhasil diupload.</p>";
                } elseif ($uploadOk) {
                    $uploadMessage = "<p class='bg-red-100 text-red-700 p-4 rounded-md mb-4'>Maaf, file gagal di kirim.</p>";
                }

                $_SESSION['uploadMessage'] = $uploadMessage;
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }

            if (isset($_SESSION['uploadMessage'])) {
                echo $_SESSION['uploadMessage'];
                unset($_SESSION['uploadMessage']);
            }
        ?>

        <section>
            <h2 class="text-2xl font-semibold mb-4 text-slate-800">Cari dan Download Kiriman</h2>
            <form class="search bg-white p-4 rounded-lg shadow-md mb-6" method="get" action="#">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="text" name="search" placeholder="Cari file..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" class="flex-1 p-2 border border-slate-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-400">
                    <button type="submit" class="bg-cyan-400 hover:bg-cyan-500 text-white px-6 py-2 rounded-md font-medium transition-colors">Cari</button>
                </div>
            </form>

            <?php
            $dir = "../uploads/hasil_kirim_ksp/";
            $files = [];
            if (is_dir($dir)) {
                $search = isset($_GET['search']) ? strtolower($_GET['search']) : '';
                foreach (scandir($dir) as $file) {
                    $filePath = $dir . $file;
                    if (is_file($filePath)) {
                        if ($search === '' || strpos(strtolower($file), $search) !== false) {
                            $files[] = $file;
                        }
                    }
                }
            }

            if (!empty($files)) {
                echo "<h2 class='text-xl font-semibold mb-4 text-slate-800'>List file kiriman</h2>";
                echo "<ul class='space-y-2'>";
                foreach ($files as $file) {
                    $fileUrl = $dir . rawurlencode($file);
                    echo "<li class='bg-white p-4 rounded-md shadow-sm border-l-4 border-cyan-400'>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='download_file' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                            <button type='submit' name='show_download' class='text-cyan-400 hover:text-cyan-500 font-medium transition-colors w-full text-left'>".htmlspecialchars($file)."</button>
                        </form>
                    </li>";
                }
                echo "</ul>";
            } else {
                echo "<p class='text-slate-600'>Tidak ada file ditemukan.</p>";
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_download'], $_POST['download_file'])) {
                $selectedFile = basename($_POST['download_file']);
                $selectedPath = $dir . $selectedFile;
                if (is_file($selectedPath)) {
                    echo "<div class='bg-white p-6 rounded-lg shadow-md mt-6'>
                        <form method='post'>
                            <input type='hidden' name='download_file' value='" . htmlspecialchars($selectedFile, ENT_QUOTES) . "'>
                            <button type='submit' name='download' class='bg-cyan-400 hover:bg-cyan-500 text-white px-6 py-2 rounded-md font-medium transition-colors'>Unduh ".htmlspecialchars($selectedFile)."</button>
                        </form>
                    </div>";
                }
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'], $_POST['download_file'])) {
                $downloadFile = basename($_POST['download_file']);
                $downloadPath = $dir . $downloadFile;
                if (is_file($downloadPath)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($downloadPath).'"');
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
        </section>
    </main>
    <footer class="bg-emerald-500 text-white py-6 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 KSP Manager</p>
        </div>
    </footer>
</body>
</html>
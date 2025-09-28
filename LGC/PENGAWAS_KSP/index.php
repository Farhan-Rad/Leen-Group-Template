<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KSP Manager</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <header class="bg-emerald-400 p-6">
        <h1>KSP Manager</h1>
        <nav>
            <ul>
                <li><a href="about.php">Tentang</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Upload</h2>
        <form action="index.php" method="post" enctype="multipart/form-data">
            Pilih file untuk di Upload:
            <input type="file" name="fileToUpload" id="fileToUpload">
            <input type="submit" value="Upload File" name="submit">
        </form>

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
                    $uploadMessage = "<p>Terjadi kesalahan saat mengupload file.</p>";
                    $uploadOk = 0;
                }

                if ($uploadOk && move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    $uploadMessage = "<p>File ". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) ." berhasil diupload.</p>";
                } elseif ($uploadOk) {
                    $uploadMessage = "<p>Maaf, file gagal diupload.</p>";
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

        <h2>Cari dan Download Template</h2>
        <form class="search" method="get" action="#">
            <input type="text" name="search" placeholder="Cari file..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Cari</button>
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
            echo "<h2>List file template</h2>";
            echo "<ul>";
            foreach ($files as $file) {
                $fileUrl = $dir . rawurlencode($file);
                echo "<li>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='download_file' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                        <button type='submit' name='show_download'>".htmlspecialchars($file)."</button>
                    </form>
                </li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Tidak ada file ditemukan.</p>";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_download'], $_POST['download_file'])) {
            $selectedFile = basename($_POST['download_file']);
            $selectedPath = $dir . $selectedFile;
            if (is_file($selectedPath)) {
                echo "<form method='post'>
                    <input type='hidden' name='download_file' value='" . htmlspecialchars($selectedFile, ENT_QUOTES) . "'>
                    <button type='submit' name='download'>Unduh ".htmlspecialchars($selectedFile)."</button>
                </form>";
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

        <form action="index.php" method="post" enctype="multipart/form-data">
            Pilih file untuk di Kirim:
            <input type="file" name="fileToKirim" id="fileToKirim">
            <input type="submit" value="Kirim File" name="submit">
        </form>

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
                    $uploadMessage = "<p>Terjadi kesalahan saat mengirim file.</p>";
                    $uploadOk = 0;
                }

                if ($uploadOk && move_uploaded_file($_FILES["fileToKirim"]["tmp_name"], $target_file)) {
                    $uploadMessage = "<p>File ". htmlspecialchars(basename($_FILES["fileToKirim"]["name"])) ." berhasil diupload.</p>";
                } elseif ($uploadOk) {
                    $uploadMessage = "<p>Maaf, file gagal di kirim.</p>";
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

        <h2>Cari dan Download Kiriman</h2>
        <form class="search" method="get" action="#">
            <input type="text" name="search" placeholder="Cari file..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Cari</button>
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
            echo "<h2>List file kiriman</h2>";
            echo "<ul>";
            foreach ($files as $file) {
                $fileUrl = $dir . rawurlencode($file);
                echo "<li>
                    <form method='post' style='display:inline;'>
                        <input type='hidden' name='download_file' value='" . htmlspecialchars($file, ENT_QUOTES) . "'>
                        <button type='submit' name='show_download'>".htmlspecialchars($file)."</button>
                    </form>
                </li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Tidak ada file ditemukan.</p>";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_download'], $_POST['download_file'])) {
            $selectedFile = basename($_POST['download_file']);
            $selectedPath = $dir . $selectedFile;
            if (is_file($selectedPath)) {
                echo "<form method='post'>
                    <input type='hidden' name='download_file' value='" . htmlspecialchars($selectedFile, ENT_QUOTES) . "'>
                    <button type='submit' name='download'>Unduh ".htmlspecialchars($selectedFile)."</button>
                </form>";
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
    </main>
    <footer>
        <p>&copy; 2025 KSP Manager</p>
    </footer>
</body>
</html>
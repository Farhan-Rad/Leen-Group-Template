<?php
// upload_materi.php
// Halaman untuk Guru/Pengawas mengupload materi, KSP tugas, contoh KSP, dan KSP pembikinan tugas
// Juga menyediakan download untuk Sekolah Binaan/Guru/Siswa

// Konfigurasi direktori upload (pastikan folder ini ada dan writable)
$uploadDir = 'uploads/';
$allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'jpg', 'png']; // Sesuaikan dengan kebutuhan
$maxFileSize = 10 * 1024 * 1024; // 10MB

// Buat direktori jika belum ada
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Subdirektori untuk organisasi
$subDirs = [
    'materi' => $uploadDir . 'materi/',
    'ksp_tugas' => $uploadDir . 'ksp_tugas/',
    'contoh_ksp' => $uploadDir . 'contoh_ksp/',
    'pembikinan_tugas' => $uploadDir . 'pembikinan_tugas/'
];

foreach ($subDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Fungsi untuk handle upload
function handleUpload($targetDir, $fieldName) {
    global $allowedExtensions, $maxFileSize;
    
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error upload file.'];
    }
    
    $file = $_FILES[$fieldName];
    $fileName = basename($file['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = date('Y-m-d_H-i-s') . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $fileName);
    $targetFile = $targetDir . $newFileName;
    
    // Validasi ukuran
    if ($file['size'] > $maxFileSize) {
        return ['success' => false, 'message' => 'File terlalu besar (maks 10MB).'];
    }
    
    // Validasi ekstensi
    if (!in_array($fileExt, $allowedExtensions)) {
        return ['success' => false, 'message' => 'Ekstensi file tidak diizinkan.'];
    }
    
    // Upload file
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'message' => 'File berhasil diupload: ' . $fileName, 'filename' => $newFileName];
    } else {
        return ['success' => false, 'message' => 'Gagal upload file.'];
    }
}

// Handle POST requests untuk upload
$uploadResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'upload_materi':
            $uploadResult = handleUpload($subDirs['materi'], 'file_materi');
            break;
        case 'upload_ksp_tugas':
            $uploadResult = handleUpload($subDirs['ksp_tugas'], 'file_ksp_tugas');
            break;
        case 'upload_contoh_ksp':
            $uploadResult = handleUpload($subDirs['contoh_ksp'], 'file_contoh_ksp');
            break;
        case 'upload_pembikinan_tugas':
            $uploadResult = handleUpload($subDirs['pembikinan_tugas'], 'file_pembikinan_tugas');
            break;
        default:
            $uploadResult = ['success' => false, 'message' => 'Aksi tidak valid.'];
    }
}

// Fungsi untuk mendapatkan daftar file di direktori (untuk download)
function getFilesInDir($dir) {
    $files = [];
    if (is_dir($dir)) {
        $scannedDir = scandir($dir);
        foreach ($scannedDir as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $dir . $file;
                if (is_file($filePath)) {
                    $files[] = [
                        'name' => $file,
                        'size' => filesize($filePath),
                        'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                        'url' => basename($dir) . '/' . $file // Relative URL untuk download
                    ];
                }
            }
        }
        // Urutkan berdasarkan tanggal terbaru
        usort($files, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
    }
    return $files;
}

// Ambil daftar file untuk setiap kategori
$filesMateri = getFilesInDir($subDirs['materi']);
$filesKspTugas = getFilesInDir($subDirs['ksp_tugas']);
$filesContohKsp = getFilesInDir($subDirs['contoh_ksp']);
$filesPembikinanTugas = getFilesInDir($subDirs['pembikinan_tugas']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Materi - Guru/Pengawas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        .tabs { display: flex; margin-bottom: 20px; }
        .tab { padding: 10px 20px; background: #ddd; cursor: pointer; border: none; margin-right: 5px; border-radius: 5px 5px 0 0; }
        .tab.active { background: #007bff; color: white; }
        .tab-content { display: none; padding: 20px; border: 1px solid #ddd; border-top: none; border-radius: 0 0 5px 5px; }
        .tab-content.active { display: block; }
        form { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .file-list { margin-top: 20px; }
        .file-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border: 1px solid #eee; margin-bottom: 5px; border-radius: 4px; }
        .file-info { flex: 1; }
        .download-btn { background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
        .download-btn:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Upload Materi & KSP - Guru/Pengawas</h1>
        
        <?php if ($uploadResult): ?>
            <div class="message <?php echo $uploadResult['success'] ? 'success' : 'error'; ?>">
                <?php echo $uploadResult['message']; ?>
            </div>
        <?php endif; ?>

        <!-- Tab Navigation -->
        <div class="tabs">
            <button class="tab active" onclick="showTab('upload-materi')">Upload Materi</button>
            <button class="tab" onclick="showTab('upload-ksp-tugas')">Kirim KSP Tugas</button>
            <button class="tab" onclick="showTab('upload-contoh-ksp')">Upload Contoh KSP</button>
            <button class="tab" onclick="showTab('upload-pembikinan-tugas')">Upload KSP Pembikinan Tugas</button>
            <button class="tab" onclick="showTab('download')">Download Files</button>
        </div>

        <!-- Upload Materi -->
        <div id="upload-materi" class="tab-content active">
            <h2>Upload Materi Pelajaran</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_materi">
                <label for="file_materi">Pilih File Materi:</label>
                <input type="file" id="file_materi" name="file_materi" required accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar,.jpg,.png">
                <button type="submit">Upload Materi</button>
            </form>
        </div>

        <!-- Upload KSP Tugas -->
        <div id="upload-ksp-tugas" class="tab-content">
            <h2>Kirim KSP (Tugas)</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_ksp_tugas">
                <label for="file_ksp_tugas">Pilih File KSP Tugas:</label>
                <input type="file" id="file_ksp_tugas" name="file_ksp_tugas" required accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar">
                <button type="submit">Kirim KSP Tugas</button>
            </form>
        </div>

        <!-- Upload Contoh KSP -->
        <div id="upload-contoh-ksp" class="tab-content">
            <h2>Upload Contoh KSP (Tugas)</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_contoh_ksp">
                <label for="file_contoh_ksp">Pilih File Contoh KSP:</label>
                <input type="file" id="file_contoh_ksp" name="file_contoh_ksp" required accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar">
                <button type="submit">Upload Contoh KSP</button>
            </form>
        </div>

        <!-- Upload Pembikinan Tugas -->
        <div id="upload-pembikinan-tugas" class="tab-content">
            <h2>Upload KSP (Pembikinan Tugas)</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_pembikinan_tugas">
                <label for="file_pembikinan_tugas">Pilih File Pembikinan Tugas:</label>
                <input type="file" id="file_pembikinan_tugas" name="file_pembikinan_tugas" required accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.rar">
                <button type="submit">Upload Pembikinan Tugas</button>
            </form>
        </div>

        <!-- Download Section -->
        <div id="download" class="tab-content">
            <h2>Download Files (untuk Sekolah Binaan/Guru/Siswa)</h2>
            
            <h3>Materi Pelajaran</h3>
            <div class="file-list">
                <?php if (empty($filesMateri)): ?>
                    <p>Tidak ada file materi.</p>
                <?php else: ?>
                    <?php foreach ($filesMateri as $file): ?>
                        <div class="file-item">
                            <div class="file-info">
                                <strong><?php echo htmlspecialchars($file['name']); ?></strong><br>
                                Ukuran: <?php echo number_format($file['size'] / 1024, 2); ?> KB | Tanggal: <?php echo $file['date']; ?>
                            </div>
                            <a href="uploads/materi/<?php echo $file['name']; ?>" class="download-btn" download>Download</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <h3>KSP Tugas</h3>
            <div class="file-list">
                <?php if (empty($filesKspTugas)): ?>
                    <p>Tidak ada file KSP tugas.</p>
                <?php else: ?>
                    <?php foreach ($filesKspTugas as $file): ?>
                        <div class="file-item">
                            <div class="file-info">
                                <strong><?php echo htmlspecialchars($file['name']); ?></strong><br>
                                Ukuran: <?php echo number_format($file['size'] / 1024, 2); ?> KB | Tanggal: <?php echo $file['date']; ?>
                            </div>
                            <a href="uploads/ksp_tugas/<?php echo $file['name']; ?>" class="download-btn" download>Download</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <h3>Contoh KSP</h3>
            <div class="file-list">
                <?php if (empty($filesContohKsp)): ?>
                    <p>Tidak ada file contoh KSP.</p>
                <?php else: ?>
                    <?php foreach ($filesContohKsp as $file): ?>
                        <div class="file-item">
                            <div class="file-info">
                                <strong><?php echo htmlspecialchars($file['name']); ?></strong><br>
                                Ukuran: <?php echo number_format($file['size'] / 1024, 2); ?> KB | Tanggal: <?php echo $file['date']; ?>
                            </div>
                            <a href="uploads/contoh_ksp/<?php echo $file['name']; ?>" class="download-btn" download>Download</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <h3>Pembikinan Tugas</h3>
            <div class="file-list">
                <?php if (empty($filesPembikinanTugas)): ?>
                    <p>Tidak ada file pembikinan tugas.</p>
                <?php else: ?>
                    <?php foreach ($filesPembikinanTugas as $file): ?>
                        <div class="file-item">
                            <div class="file-info">
                                <strong><?php echo htmlspecialchars($file['name']); ?></strong><br>
                                Ukuran: <?php echo number_format($file['size'] / 1024, 2); ?> KB | Tanggal: <?php echo $file['date']; ?>
                            </div>
                            <a href="uploads/pembikinan_tugas/<?php echo $file['name']; ?>" class="download-btn" download>Download</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));
            
            // Remove active class from all tabs
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
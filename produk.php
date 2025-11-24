<?php
// Izinkan CORS dari sumber manapun
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-Type: application/json');

// --- Konfigurasi Koneksi Database ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'toko_api';

// Buat koneksi ke database menggunakan PDO
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Jika koneksi gagal, kirim pesan error dan hentikan script
    echo json_encode(['status' => false, 'data' => "Koneksi database gagal: " . $e->getMessage()]);
    exit();
}

// Dapatkan metode request (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];
// Dapatkan ID dari query string (misal: produk.php?id=1)
$id = $_GET['id'] ?? null;

// --- Logika Utama Berdasarkan Metode Request ---

if ($method == 'GET') {
    // Logika untuk MENAMPILKAN semua produk
    $sql = "SELECT * FROM products ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => true, 'data' => $products]);

} elseif ($method == 'POST') {
    // Logika untuk MENAMBAH produk baru
    $data = json_decode(file_get_contents('php://input'), true);
    $kode_produk = $data['kode_produk'] ?? '';
    $nama_produk = $data['nama_produk'] ?? '';
    $harga = $data['harga'] ?? 0;

    if (empty($kode_produk) || empty($nama_produk) || empty($harga)) {
        echo json_encode(['status' => false, 'data' => 'Semua field harus diisi.']);
        exit();
    }

    try {
        $sql = "INSERT INTO products (kode_produk, nama_produk, harga) VALUES (:kode, :nama, :harga)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['kode' => $kode_produk, 'nama' => $nama_produk, 'harga' => $harga]);
        echo json_encode(['status' => true, 'data' => 'Produk berhasil ditambahkan.']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Error untuk duplikat entry
            echo json_encode(['status' => false, 'data' => 'Kode produk sudah ada.']);
        } else {
            echo json_encode(['status' => false, 'data' => 'Server error: ' . $e->getMessage()]);
        }
    }

} elseif ($method == 'PUT') {
    // Logika untuk MENGEDIT produk
    if (!$id) {
        echo json_encode(['status' => false, 'data' => 'ID produk tidak ditemukan untuk diedit.']);
        exit();
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $kode_produk = $data['kode_produk'] ?? '';
    $nama_produk = $data['nama_produk'] ?? '';
    $harga = $data['harga'] ?? 0;

    if (empty($kode_produk) || empty($nama_produk) || empty($harga)) {
        echo json_encode(['status' => false, 'data' => 'Semua field harus diisi.']);
        exit();
    }

    try {
        $sql = "UPDATE products SET kode_produk = :kode, nama_produk = :nama, harga = :harga WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['kode' => $kode_produk, 'nama' => $nama_produk, 'harga' => $harga, 'id' => $id]);
        echo json_encode(['status' => true, 'data' => 'Produk berhasil diperbarui.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'data' => 'Server error saat update: ' . $e->getMessage()]);
    }

} elseif ($method == 'DELETE') {
    // Logika untuk MENGHAPUS produk
    if (!$id) {
        echo json_encode(['status' => false, 'data' => 'ID produk tidak ditemukan untuk dihapus.']);
        exit();
    }

    try {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        echo json_encode(['status' => true, 'data' => 'Produk berhasil dihapus.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'data' => 'Server error saat hapus: ' . $e->getMessage()]);
    }
} else {
    // Jika metode request bukan GET, POST, PUT, atau DELETE
    echo json_encode(['status' => false, 'data' => 'Metode request tidak didukung.']);
}
?>
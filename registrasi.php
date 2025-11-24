<?php
// Izinkan CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-Type: application/json');

// --- 1. Konfigurasi Koneksi Database ---
$db_host = 'localhost';      // atau 127.0.0.1
$db_user = 'root';           // Username default XAMPP
$db_pass = '';               // Password default XAMPP biasanya kosong
$db_name = 'toko_api';       // Nama database Anda

// Buat koneksi menggunakan PDO (lebih aman)
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    // Set mode error PDO ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Jika koneksi gagal, kirim response error
    echo json_encode(['code' => 500, 'status' => false, 'data' => "Koneksi database gagal: " . $e->getMessage()]);
    exit();
}

// --- 2. Ambil dan Validasi Data dari Flutter ---
$data = json_decode(file_get_contents('php://input'), true);

$nama = $data['nama'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$password_konfirmasi = $data['password_confirmation'] ?? '';

if (empty($nama) || empty($email) || empty($password)) {
    echo json_encode(['code' => 400, 'status' => false, 'data' => 'Semua field harus diisi.']);
    exit();
}

if ($password !== $password_konfirmasi) {
    echo json_encode(['code' => 400, 'status' => false, 'data' => 'Konfirmasi password tidak cocok.']);
    exit();
}

// --- 3. Amankan Password dan Simpan ke Database ---
// Hashing password adalah WAJIB untuk keamanan
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Gunakan prepared statement untuk mencegah SQL Injection
try {
    $sql = "INSERT INTO users (nama, email, password) VALUES (:nama, :email, :password)";
    $stmt = $pdo->prepare($sql);
    
    // Bind parameter ke statement
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    
    // Eksekusi statement
    $stmt->execute();

    // Kirim response sukses
    echo json_encode(['code' => 200, 'status' => true, 'data' => 'Registrasi berhasil!']);

} catch (PDOException $e) {
    // Cek jika error karena email sudah ada (kode error 23000 untuk duplikat)
    if ($e->getCode() == 23000) {
        echo json_encode(['code' => 409, 'status' => false, 'data' => 'Email sudah terdaftar.']);
    } else {
        echo json_encode(['code' => 500, 'status' => false, 'data' => 'Terjadi kesalahan pada server: ' . $e->getMessage()]);
    }
}
?>
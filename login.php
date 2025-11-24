<?php
// Izinkan CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-Type: application/json');

// --- 1. Konfigurasi Koneksi Database ---
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'toko_api';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['code' => 500, 'status' => false, 'data' => "Koneksi database gagal: " . $e->getMessage()]);
    exit();
}

// --- 2. Ambil Data dari Flutter ---
$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['code' => 400, 'status' => false, 'data' => 'Email dan password harus diisi.']);
    exit();
}

// --- 3. Cari User dan Verifikasi Password ---
try {
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    // Fetch user
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek jika user ditemukan DAN password cocok
    if ($user && password_verify($password, $user['password'])) {
        // Jika login berhasil
        echo json_encode([
            'code' => 200,
            'status' => true,
            'data' => [
                'token' => 'ini-token-asli-setelah-login-' . bin2hex(random_bytes(16)), // Buat token random
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email']
                ]
            ]
        ]);
    } else {
        // Jika user tidak ditemukan atau password salah
        echo json_encode(['code' => 401, 'status' => false, 'data' => 'Email atau password salah.']);
    }

} catch (PDOException $e) {
    echo json_encode(['code' => 500, 'status' => false, 'data' => 'Terjadi kesalahan pada server: ' . $e->getMessage()]);
}
?>
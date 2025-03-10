<?php
require 'koneksi.php'; // Pastikan file koneksi sudah benar

header('Content-Type: application/json'); // Set header agar response dalam format JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tangkap data input
    $nama = isset($_POST['nama-lengkap']) ? trim(htmlspecialchars($_POST['nama-lengkap'])) : "";
    $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";

    // Validasi input tidak boleh kosong
    if (empty($nama) || empty($email) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Semua field harus diisi!"]);
        exit();
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Format email tidak valid!"]);
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Cek apakah email sudah terdaftar
    $cekEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $cekEmail->bind_param("s", $email);
    $cekEmail->execute();
    $cekEmail->store_result();

    if ($cekEmail->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email sudah digunakan!"]);
        $cekEmail->close();
        exit();
    }
    $cekEmail->close();

    // Ambil ID terakhir & buat ID baru
    $query = "SELECT iduser FROM users ORDER BY iduser DESC LIMIT 1";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $lastId = isset($row['iduser']) ? intval(substr($row['iduser'], 4)) : 0;
    $newId = "2025" . str_pad($lastId + 1, 4, "0", STR_PAD_LEFT); // Format ID 20250001, 20250002, dst.

    // Kita save ke db 
    $stmt = $conn->prepare("INSERT INTO users (iduser, nama, email, pw) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $newId, $nama, $email, $hashedPassword);

    if ($stmt->execute()) {
        header("Location:..\login.html");
        echo json_encode(["status" => "success", "message" => "Registrasi berhasil!"]);
    } else {
        
        echo json_encode(["status" => "error", "message" => "Terjadi kesalahan: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Metode tidak diizinkan!"]);
}
?>

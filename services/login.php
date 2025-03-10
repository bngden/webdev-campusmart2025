<?php
session_start();
require 'koneksi.php'; // Pastikan ada file koneksi database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? trim($_POST['username']) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";

    if (empty($username) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Username dan password harus diisi!"]);
        exit();
    }

    // Cek user di database
    $stmt = $conn->prepare("SELECT iduser, nama, pw FROM users WHERE nama = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($iduser, $nama, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION['iduser'] = $iduser;
            $_SESSION['nama'] = $nama;
            
            echo json_encode(["status" => "success", "message" => "Login berhasil!", "redirect" => "beranda.html"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password salah!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username tidak ditemukan!"]);
    }

    $stmt->close();
    $conn->close();
}
?>

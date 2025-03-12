<?php
require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    if (empty($email) || empty($password) || empty($confirmPassword)) {
        echo json_encode(["status" => "error", "message" => "Semua field harus diisi!"]);
        exit();
    }

    if ($password !== $confirmPassword) {
        echo json_encode(["status" => "error", "message" => "Password tidak cocok!"]);
        exit();
    }

    // Cek apakah email ada di database
    $cekEmail = $conn->prepare("SELECT iduser FROM users WHERE email = ?");
    $cekEmail->bind_param("s", $email);
    $cekEmail->execute();
    $result = $cekEmail->get_result();

    if ($result->num_rows > 0) {
        // Hash password baru
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update password
        $updatePassword = $conn->prepare("UPDATE users SET pw = ? WHERE email = ?");
        $updatePassword->bind_param("ss", $hashedPassword, $email);

        if ($updatePassword->execute()) {
            echo json_encode(["status" => "success", "message" => "Password berhasil diperbarui!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal memperbarui password."]);
        }

        $updatePassword->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Email tidak ditemukan!"]);
    }

    $cekEmail->close();
    $conn->close();
}
?>



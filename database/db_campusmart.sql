CREATE DATABASE db_campusmart;
USE db_campusmart;

CREATE TABLE USER (
    iduser INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(60) NOT NULL,
    email VARCHAR(60) UNIQUE NOT NULL,
    PASSWORD VARCHAR(20) NOT NULL
);

CREATE TABLE Produk (
    idproduk INT AUTO_INCREMENT PRIMARY KEY,
    namaproduk VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    jumlah INT NOT NULL,
    STATUS ENUM('habis', 'tersedia') NOT NULL
);

CREATE TABLE Transaksi (
    notransaksi INT AUTO_INCREMENT PRIMARY KEY,
    iduser INT,
    tgl DATETIME DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (iduser) REFERENCES USER(iduser) ON DELETE CASCADE
);

CREATE TABLE Detail_Transaksi (
    nodetail INT AUTO_INCREMENT PRIMARY KEY,
    notransaksi INT,
    idproduk INT,
    qty INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (notransaksi) REFERENCES Transaksi(notransaksi) ON DELETE CASCADE,
    FOREIGN KEY (idproduk) REFERENCES Produk(idproduk) ON DELETE CASCADE
);


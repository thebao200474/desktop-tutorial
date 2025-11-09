-- ChemLearn database schema
CREATE DATABASE IF NOT EXISTS chemlearn CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE chemlearn;

CREATE TABLE IF NOT EXISTS nguoidung (
  ma_user INT AUTO_INCREMENT PRIMARY KEY,
  hoten VARCHAR(100),
  tendangnhap VARCHAR(50) UNIQUE,
  matkhau VARCHAR(255),
  quyen ENUM('admin','user') DEFAULT 'user'
);

CREATE TABLE IF NOT EXISTS baigiang (
  ma_baigiang INT AUTO_INCREMENT PRIMARY KEY,
  ten_baigiang VARCHAR(200),
  noidung TEXT,
  ma_user INT
);

CREATE TABLE IF NOT EXISTS cauhoi (
  ma_cauhoi INT AUTO_INCREMENT PRIMARY KEY,
  noidung TEXT,
  dapan_a VARCHAR(255),
  dapan_b VARCHAR(255),
  dapan_c VARCHAR(255),
  dapan_d VARCHAR(255),
  dapandung CHAR(1),
  ma_baigiang INT
);

CREATE TABLE IF NOT EXISTS nguyento (
  ma_nguyento INT AUTO_INCREMENT PRIMARY KEY,
  ten VARCHAR(100),
  kyhieu VARCHAR(10),
  nguyentukhoi FLOAT,
  nhom INT,
  chuky INT,
  mota TEXT
);

CREATE TABLE IF NOT EXISTS phanung (
  ma_phanung INT AUTO_INCREMENT PRIMARY KEY,
  mota TEXT,
  sanpham TEXT,
  madieukien VARCHAR(50),
  ma_nguyento INT
);

CREATE TABLE IF NOT EXISTS hoi_dap_ai (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ma_user INT,
  cau_hoi TEXT,
  cau_tra_loi TEXT,
  thoigian DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tien_do_hoc (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ma_user INT,
  ma_baigiang INT,
  so_cau_dung INT,
  so_cau_sai INT,
  ngay_lam DATE
);

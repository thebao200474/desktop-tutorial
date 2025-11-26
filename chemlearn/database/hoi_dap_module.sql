-- Bảng hỏi đáp ChemLearn
CREATE TABLE IF NOT EXISTS cau_hoi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  tieu_de VARCHAR(255) NOT NULL,
  noi_dung_html TEXT NOT NULL,
  trang_thai ENUM('open','solved') DEFAULT 'open',
  luot_xem INT DEFAULT 0,
  so_cau_tra_loi INT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cau_tra_loi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cau_hoi_id INT NOT NULL,
  user_id INT,
  noi_dung_html TEXT NOT NULL,
  is_best TINYINT(1) DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS file_dinh_kem (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cau_hoi_id INT,
  duong_dan VARCHAR(255),
  ten_goc VARCHAR(255),
  FOREIGN KEY (cau_hoi_id) REFERENCES cau_hoi(id) ON DELETE CASCADE
);

INSERT INTO cau_hoi (tieu_de, noi_dung_html, trang_thai)
VALUES
  ('Làm sao tính pH khi pha loãng dung dịch?', '<p>Mình cần công thức chuyển từ 2M về 0.5M.</p>', 'open'),
  ('So sánh tính oxy hóa của KMnO4 và K2Cr2O7?', '<p>Trong môi trường axit, thuốc thử nào mạnh hơn?</p>', 'open')
ON DUPLICATE KEY UPDATE tieu_de = VALUES(tieu_de);

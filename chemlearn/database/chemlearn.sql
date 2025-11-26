
-- -----------------------------------------------------
--  Module hỏi đáp ChemLearn
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS cau_hoi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
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
  user_id INT NULL,
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

INSERT INTO cau_hoi (id, tieu_de, noi_dung_html, trang_thai, luot_xem, so_cau_tra_loi)
VALUES
  (1, 'Làm sao tính nhanh pH của dung dịch sau pha loãng?', '<p>Mình có dung dịch HCl 2M, muốn pha về 0,5M thì pha thêm bao nhiêu nước?</p>', 'open', 5, 0)
ON DUPLICATE KEY UPDATE tieu_de = VALUES(tieu_de);

INSERT INTO cau_tra_loi (id, cau_hoi_id, noi_dung_html, is_best)
VALUES
  (1, 1, '<p>Dùng công thức C1V1 = C2V2 bạn nhé.</p>', 1)
ON DUPLICATE KEY UPDATE noi_dung_html = VALUES(noi_dung_html);

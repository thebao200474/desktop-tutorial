-- --------------------------------------------------
-- Bảng phương trình hóa học cho ChemLearn
-- --------------------------------------------------

CREATE TABLE IF NOT EXISTS phuongtrinh (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phuong_trinh VARCHAR(255) NOT NULL,
    loai_phan_ung VARCHAR(100) NOT NULL,
    giai_thich TEXT NOT NULL,
    nhom_phan_ung VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO phuongtrinh (id, phuong_trinh, loai_phan_ung, giai_thich, nhom_phan_ung) VALUES
    (1, '2H₂ + O₂ → 2H₂O', 'Hóa hợp', 'Hydro cháy trong oxy tạo thành nước – phản ứng hóa hợp tỏa nhiều nhiệt.', 'Vô cơ'),
    (2, 'N₂ + 3H₂ → 2NH₃', 'Hóa hợp', 'Tổng hợp amoniac (Haber) – phản ứng quan trọng trong công nghiệp phân bón.', 'Vô cơ'),
    (3, 'CaCO₃ → CaO + CO₂', 'Phân hủy', 'Nung đá vôi tạo vôi sống và khí cacbonic – phản ứng trong sản xuất xi măng.', 'Vô cơ'),
    (4, 'Zn + 2HCl → ZnCl₂ + H₂', 'Thế', 'Kẽm đẩy hydro ra khỏi axit clohiđric tạo khí H₂.', 'Vô cơ'),
    (5, 'AgNO₃ + NaCl → AgCl + NaNO₃', 'Trao đổi', 'Phản ứng tạo kết tủa bạc clorua màu trắng.', 'Vô cơ'),
    (6, 'CuO + H₂ → Cu + H₂O', 'Oxi hóa – khử', 'Đồng(II) oxit bị hydro khử thành đồng kim loại.', 'Vô cơ'),
    (7, 'CH₄ + 2O₂ → CO₂ + 2H₂O', 'Đốt cháy', 'Đốt cháy metan sinh năng lượng – phản ứng cơ bản trong hô hấp và công nghiệp.', 'Hữu cơ'),
    (8, 'CH₂=CH₂ + H₂ → CH₃–CH₃', 'Cộng', 'Hydro hóa etilen tạo etan, phản ứng đặc trưng của anken.', 'Hữu cơ'),
    (9, 'CH₃COOH + C₂H₅OH ⇌ CH₃COOC₂H₅ + H₂O', 'Este hóa', 'Axit axetic tác dụng với etanol tạo etyl axetat (mùi thơm).', 'Hữu cơ'),
    (10, 'CH₃CH₂OH → CH₂=CH₂ + H₂O', 'Tách nước', 'Tách nước từ etanol tạo etilen ở 170°C (xúc tác H₂SO₄ đặc).', 'Hữu cơ')
ON DUPLICATE KEY UPDATE
    phuong_trinh = VALUES(phuong_trinh),
    loai_phan_ung = VALUES(loai_phan_ung),
    giai_thich = VALUES(giai_thich),
    nhom_phan_ung = VALUES(nhom_phan_ung);

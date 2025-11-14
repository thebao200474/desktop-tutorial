CREATE TABLE IF NOT EXISTS faq_hoa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cau_hoi VARCHAR(255),
    tu_khoa VARCHAR(255),
    cau_tra_loi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO faq_hoa (cau_hoi, tu_khoa, cau_tra_loi) VALUES
('Định nghĩa axit theo Bronsted–Lowry là gì?', 'axit;bronsted;proton',
 'Theo thuyết Bronsted–Lowry, axit là chất có khả năng cho proton (H⁺).'),
('Số oxi hóa của O trong H2O là bao nhiêu?', 'số oxi hóa;H2O;O',
 'Trong H₂O, số oxi hóa của oxy là -2.'),
('Liên kết ion hình thành như thế nào?', 'liên kết ion;cation;anion',
 'Liên kết ion xuất hiện khi electron được chuyển từ kim loại sang phi kim, tạo cation và anion hút nhau.');

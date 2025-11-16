-- ------------------------------------------------------------
-- Cơ sở dữ liệu mẫu cho dự án ChemLearn (CT275 - Đại học Cần Thơ)
-- Có thể import trực tiếp trong phpMyAdmin/XAMPP để khởi tạo dữ liệu.
-- ------------------------------------------------------------

DROP TABLE IF EXISTS lessons;
DROP TABLE IF EXISTS chude;

CREATE TABLE chude (
    ma_chude INT AUTO_INCREMENT PRIMARY KEY,
    ten_chude VARCHAR(150) NOT NULL,
    mota TEXT NULL,
    icon VARCHAR(80) NOT NULL DEFAULT 'fa-flask',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ma_chude INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    content MEDIUMTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_lessons_topic FOREIGN KEY (ma_chude) REFERENCES chude(ma_chude) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Dữ liệu chủ đề minh họa
-- ------------------------------------------------------------
INSERT INTO chude (ten_chude, mota, icon) VALUES
('Công thức Hóa học', 'Tổng hợp các công thức tính toán và định luật cơ bản trong hóa học phổ thông.', 'fa-flask-vial'),
('Hóa học Hữu cơ', 'Khám phá cấu trúc, danh pháp và phản ứng đặc trưng của hợp chất hữu cơ.', 'fa-leaf'),
('Hóa học Vô cơ', 'Ôn tập kim loại, phi kim và hợp chất vô cơ quan trọng.', 'fa-mountain-sun'),
('Phản ứng & PTHH', 'Kho bài giảng cân bằng phương trình và phân loại phản ứng hóa học.', 'fa-scale-balanced'),
('Bảng tuần hoàn', 'Tổng quan 118 nguyên tố với xu hướng biến thiên các đại lượng hóa học.', 'fa-table'),
('Trắc nghiệm ôn tập', 'Bài tập trắc nghiệm tự đánh giá kiến thức theo từng chủ đề.', 'fa-puzzle-piece');

-- ------------------------------------------------------------
-- Dữ liệu bài giảng mẫu (mỗi chủ đề vài bài để thử nghiệm phân trang)
-- ------------------------------------------------------------
INSERT INTO lessons (ma_chude, title, slug, content, updated_at) VALUES
(1, 'Định luật bảo toàn khối lượng', 'dinh-luat-bao-toan-khoi-luong', 'Nội dung mẫu: trình bày phát biểu, ví dụ minh họa và bài tập vận dụng.', NOW()),
(1, 'Định luật Avogadro & khí lý tưởng', 'dinh-luat-avogadro', 'Nội dung mẫu: giới thiệu hằng số Avogadro, phương trình trạng thái khí lý tưởng.', NOW()),
(2, 'Chuỗi phản ứng ankan', 'chuoi-phan-ung-ankan', 'Tóm tắt tính chất, phản ứng đặc trưng và ứng dụng của dãy đồng đẳng ankan.', NOW()),
(2, 'Danh pháp ester', 'danh-phap-ester', 'Cách gọi tên và viết công thức cấu tạo ester theo IUPAC.', NOW()),
(3, 'Tính chất hóa học của kim loại kiềm', 'tinh-chat-kim-loai-kiem', 'Mô tả tính khử mạnh, phản ứng với nước và không khí của kim loại kiềm.', NOW()),
(3, 'Cấu tạo tinh thể ion', 'cau-tao-tinh-the-ion', 'Phân tích cấu trúc mạng tinh thể ion và ứng dụng thực tế.', NOW()),
(4, 'Phản ứng oxi hóa - khử', 'phan-ung-oxi-hoa-khu', 'Cách xác định số oxi hóa và cân bằng phản ứng oxi hóa - khử.', NOW()),
(4, 'Cân bằng phản ứng bằng phương pháp thăng bằng electron', 'can-bang-phan-ung-bang-electron', 'Các bước cụ thể cùng ví dụ thực hành.', NOW()),
(5, 'Xu hướng biến thiên bán kính nguyên tử', 'xu-huong-ban-kinh-nguyen-tu', 'Biểu diễn sự biến thiên bán kính nguyên tử theo chu kỳ và nhóm.', NOW()),
(5, 'Độ âm điện và ý nghĩa', 'do-am-dien-va-y-nghia', 'Giải thích khái niệm độ âm điện, cách so sánh và ứng dụng.', NOW()),
(6, 'Trắc nghiệm tổng hợp chương Oxi - Lưu huỳnh', 'trac-nghiem-oxi-luu-huynh', 'Bộ câu hỏi trắc nghiệm nhiều lựa chọn giúp luyện tập nhanh.', NOW()),
(6, 'Ôn tập chương Nitơ - Photpho', 'on-tap-nito-photpho', 'Tổng hợp bài tập trắc nghiệm có đáp án chi tiết.', NOW());

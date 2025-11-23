CREATE TABLE IF NOT EXISTS faq_hoa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cau_hoi VARCHAR(255),
    tu_khoa VARCHAR(255),
    cau_tra_loi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO faq_hoa (cau_hoi, tu_khoa, cau_tra_loi) VALUES
('Định nghĩa axit theo Bronsted–Lowry là gì?', 'axit; bronsted; proton', 'Axit theo Bronsted–Lowry là chất có khả năng nhường proton (H+).'),
('Số oxi hóa của O trong H₂O là bao nhiêu?', 'số oxi hóa; h2o; o', 'Số oxi hóa của O trong phân tử H₂O là −2.'),
('Liên kết ion hình thành như thế nào?', 'liên kết ion; ion; cation; anion', 'Liên kết ion là lực hút tĩnh điện giữa ion dương (cation) và ion âm (anion).'),
('Axit Arrhenius là gì?', 'axit; arrhenius; định nghĩa', 'Axit theo Arrhenius là chất khi tan trong nước phân li ra H⁺.'),
('Bazơ Arrhenius là gì?', 'bazơ; arrhenius; định nghĩa', 'Bazơ theo Arrhenius là chất khi tan trong nước phân li ra OH⁻.'),
('pH là gì?', 'pH; tính toán; nồng độ H+', 'pH là đại lượng đo nồng độ ion H⁺, pH = −log[H⁺].'),
('Cấu hình electron của natri?', 'cấu hình electron; natri; Na', 'Cấu hình electron của Na (Z = 11) là 1s² 2s² 2p⁶ 3s¹.'),
('Kim loại hoạt động mạnh nhất là gì?', 'kim loại; mạnh nhất; hoạt động hóa học', 'Kim loại hoạt động mạnh nhất là Francium (Fr), trong thực tế thường xét Cesium (Cs).'),
('Phi kim mạnh nhất?', 'phi kim; mạnh nhất; độ âm điện', 'Fluor (F) là phi kim mạnh nhất và có độ âm điện lớn nhất.'),
('Oxit lưỡng tính là gì?', 'oxit lưỡng tính; Al2O3; ZnO; ví dụ', 'Oxit lưỡng tính phản ứng được với cả axit mạnh và bazơ mạnh, ví dụ Al₂O₃, ZnO.'),
('Nước cứng là gì?', 'nước cứng; định nghĩa; Ca2+; Mg2+', 'Nước cứng là nước chứa nhiều ion Ca²⁺ và Mg²⁺.'),
('Alkane có công thức chung gì?', 'alkane; công thức chung; CnH2n+2; hydrocacbon no', 'Alkane là hydrocacbon no, mạch hở với công thức chung CnH2n+2 (n ≥ 1).'),
('Este là gì?', 'este; định nghĩa; nhóm chức; COO', 'Este là hợp chất hữu cơ có nhóm chức COO, tạo từ axit và ancol.'),
('Phản ứng xà phòng hóa là gì?', 'phản ứng; xà phòng hóa; ester; NaOH; xà phòng', 'Xà phòng hóa là phản ứng thủy phân este trong môi trường kiềm tạo muối axit béo (xà phòng) và alcohol.'),
('Polime là gì?', 'polime; định nghĩa; mắt xích; phân tử khối lớn', 'Polime là hợp chất có phân tử khối rất lớn gồm nhiều mắt xích lặp lại liên kết với nhau.'),
('Hiện tượng Tyndall là gì?', 'hiện tượng Tindall; keo; ánh sáng; nhận biết', 'Hiện tượng Tyndall là sự tán xạ ánh sáng khi truyền qua dung dịch keo.'),
('Formaldehyde có công thức gì?', 'công thức; formaldehyde; HCHO; metanal', 'Formaldehyde (metanal) có công thức HCHO.'),
('Thành phần chính của không khí?', 'thành phần; không khí; N2; O2', 'Không khí chủ yếu gồm N₂ (~78%) và O₂ (~21%).'),
('Số oxi hóa tối đa của lưu huỳnh trong H₂SO₄?', 'số oxi hóa; lưu huỳnh; H2SO4; tối đa', 'Trong H₂SO₄, lưu huỳnh có số oxi hóa +6.'),
('Nguyên tắc Le Chatelier nói gì?', 'nguyên tắc Le Chatelier; cân bằng; yếu tố; dịch chuyển', 'Nguyên tắc Le Chatelier mô tả cân bằng dịch chuyển khi hệ chịu tác động thay đổi nồng độ, áp suất hoặc nhiệt độ.'),
('Phản ứng tráng bạc nhận biết gì?', 'phản ứng tráng bạc; glucose; aldehyde; nhận biết', 'Phản ứng tráng bạc nhận biết glucose hoặc aldehyde với AgNO₃/NH₃ tạo gương bạc.'),
('Ứng dụng của kim cương?', 'ứng dụng; kim cương; độ cứng; mũi khoan', 'Kim cương dùng làm đá quý và mũi khoan nhờ độ cứng rất cao.'),
('Phân biệt alkene với brom như thế nào?', 'phân biệt; alkene; bromine; mất màu', 'Alkene làm mất màu dung dịch brom, giúp phân biệt với alkane.'),
('Chất điện li mạnh là gì?', 'chất điện li; mạnh; ví dụ; phân li hoàn toàn', 'Chất điện li mạnh phân li hoàn toàn trong nước (axit mạnh, bazơ mạnh, muối tan).'),
('Phương pháp nhiệt luyện dùng cho kim loại nào?', 'nhiệt luyện; điều chế; kim loại; sau Al', 'Nhiệt luyện dùng điều chế các kim loại có tính khử trung bình và yếu (đứng sau Al trong dãy hoạt động).');

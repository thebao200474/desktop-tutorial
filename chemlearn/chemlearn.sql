-- ChemLearn database schema
CREATE DATABASE IF NOT EXISTS chemlearn CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE chemlearn;

CREATE TABLE IF NOT EXISTS nguoidung (
  ma_user INT AUTO_INCREMENT PRIMARY KEY,
  hoten VARCHAR(100),
  tendangnhap VARCHAR(50) UNIQUE,
  matkhau VARCHAR(255),
  quyen ENUM('admin','user') DEFAULT 'user',
  diem_rank INT DEFAULT 0
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
  ngay_lam DATE,
  ghi_chu VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS de_thi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ten_de VARCHAR(255) NOT NULL,
  nguon VARCHAR(255),
  mo_ta TEXT,
  ma_de VARCHAR(50),
  nam INT,
  ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cau_hoi_de_thi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  de_thi_id INT NOT NULL,
  noi_dung TEXT NOT NULL,
  dapan_a TEXT,
  dapan_b TEXT,
  dapan_c TEXT,
  dapan_d TEXT,
  dap_an CHAR(1),
  FOREIGN KEY (de_thi_id) REFERENCES de_thi(id) ON DELETE CASCADE
);

INSERT INTO nguyento (ma_nguyento, ten, kyhieu, nguyentukhoi, nhom, chuky, mota) VALUES
  (1, 'Hiđro', 'H', 1.008, 1, 1, 'Nguyên tố phổ biến nhất trong vũ trụ, có thể tạo thành hợp chất với hầu hết nguyên tố khác.'),
  (2, 'Heli', 'He', 4.0026, 18, 1, 'Khí hiếm không màu, không mùi, dùng trong bóng bay và môi trường bảo vệ cho hàn.'),
  (3, 'Liti', 'Li', 6.94, 1, 2, 'Kim loại kiềm nhẹ, ứng dụng trong pin lithium-ion.'),
  (4, 'Berili', 'Be', 9.0122, 2, 2, 'Kim loại kiềm thổ cứng, nhẹ, dùng làm hợp kim trong ngành hàng không.'),
  (5, 'Bo', 'B', 10.81, 13, 2, 'Á kim dùng trong sản xuất thủy tinh chịu nhiệt và phân bón vi lượng.'),
  (6, 'Cacbon', 'C', 12.01, 14, 2, 'Tạo nên đa số hợp chất hữu cơ, tồn tại dạng kim cương, than chì và graphene.'),
  (7, 'Nitơ', 'N', 14.01, 15, 2, 'Khí chiếm ~78% không khí, tham gia chu trình đạm và sản xuất phân bón.'),
  (8, 'Oxi', 'O', 16.00, 16, 2, 'Cần cho hô hấp, là phi kim hoạt động mạnh, tạo oxit với hầu hết kim loại.'),
  (9, 'Flo', 'F', 19.00, 17, 2, 'Phi kim hoạt động mạnh nhất, dùng trong sản xuất Teflon và thuốc nhuộm.'),
  (10, 'Neon', 'Ne', 20.18, 18, 2, 'Khí hiếm dùng cho đèn neon phát sáng màu đỏ cam đặc trưng.')
ON DUPLICATE KEY UPDATE
  ten = VALUES(ten),
  kyhieu = VALUES(kyhieu),
  nguyentukhoi = VALUES(nguyentukhoi),
  nhom = VALUES(nhom),
  chuky = VALUES(chuky),
  mota = VALUES(mota);

INSERT INTO de_thi (id, ten_de, nguon, mo_ta, ma_de, nam)
VALUES (
  1,
  'Thi thử TN THPT 2025 – Trường Lê Hoàn, Thanh Hóa',
  'Học Hóa Online',
  'Đề thi thử tốt nghiệp THPT 2025, mã đề 163, gồm 28 câu hỏi. Phần này lưu 18 câu trắc nghiệm nhiều lựa chọn.',
  '163',
  2025
)
ON DUPLICATE KEY UPDATE
  ten_de = VALUES(ten_de),
  nguon = VALUES(nguon),
  mo_ta = VALUES(mo_ta),
  ma_de = VALUES(ma_de),
  nam = VALUES(nam);

DELETE FROM cau_hoi_de_thi WHERE de_thi_id = 1;

INSERT INTO cau_hoi_de_thi (de_thi_id, noi_dung, dapan_a, dapan_b, dapan_c, dapan_d, dap_an) VALUES
(1, 'Trong cơ thể người, ion Mg2+ tham gia cấu trúc tế bào, tổng hợp protein và tổng hợp chất sinh năng lượng ATPN. Ở trạng thái cơ bản, cấu hình electron của ion Mg2+ là 1s2 2s2 2p6. Số hạt proton của ion Mg2+ là:', '8', '12', '14', '10', 'B'),
(1, 'Hợp chất C2H5NHCH3 có tên là:', 'propylamine', 'dimethylamine', 'diethylamine', 'ethylmethylamine', 'D'),
(1, 'Cho bảng giá trị thế điện cực chuẩn của các cặp oxi hóa – khử: Cu2+/Cu, Ag+/Ag, Fe2+/Fe, Ni2+/Ni, Zn2+/Zn với các giá trị lần lượt là: +0,34; +0,80; –0,44; –0,26; –0,76 (V). Sức điện động chuẩn lớn nhất của pin Galvani thiết lập từ hai cặp oxi hóa – khử trong số các cặp trên là:', '0,93 V', '1,24 V', '1,65 V', '1,56 V', 'D'),
(1, 'Sắp xếp các chất sau theo thứ tự độ ngọt tăng dần: glucose, fructose, saccharose:', 'Fructose < glucose < saccharose', 'Glucose < saccharose < fructose', 'Saccharose < fructose < glucose', 'Glucose < fructose < saccharose', 'D'),
(1, 'Điểm đẳng điện (pI) của amino acid là giá trị pH của dung dịch mà ở đó tổng số điện tích dương và âm của amino acid bằng nhau (khi đó nồng độ ion lưỡng cực đạt cực đại). Khi pH < pI thì amino acid tồn tại chủ yếu ở dạng cation, còn khi pH > pI thì amino acid tồn tại chủ yếu ở dạng anion. Cho giá trị pI của ba amino acid leucine, aspartic acid và arginine. Cho các nhận định sau: (1) Nếu đặt dung dịch chứa hỗn hợp leucine, aspartic acid và arginine ở pH = 6,04 trong một điện trường thì có thể tách riêng từng amino acid. (2) Nếu đặt dung dịch chứa hỗn hợp leucine, aspartic acid và arginine ở pH = 2,77 trong một điện trường thì có 3 amino acid dịch chuyển về điện cực âm. (3) Nếu đặt dung dịch chứa hỗn hợp leucine, aspartic acid và arginine ở pH = 10,76 trong một điện trường thì có 3 amino acid dịch chuyển về điện cực dương. (4) Trong dung dịch pH = 6,04, leucine tồn tại chủ yếu ở dạng CH3-CH2-CH(CH3)-CH(NH3+)-COO-. Số nhận định sai là:', '1', '3', '4', '2', 'D'),
(1, 'Cao su buna-S (còn gọi là cao su SBR) là loại cao su tổng hợp được sử dụng rất phổ biến. Thực hiện phản ứng trùng hợp các chất nào dưới đây thu được sản phẩm là cao su buna-S?', 'CH2=CH-CH=CH2 và C6H5CH=CH2', 'CH2=CH-CH=CH2 và CH2-CHCN', 'CH2=CH-CH=CH2 và CH2=CHCl', 'CH2=CH-CH=CH2 và sulfur', 'A'),
(1, 'Giải Nobel Hóa học năm 2010 được trao cho ba nhà hóa học Richard F. Heck, Ei-ichi Negishi và Akira Suzuki với công trình nghiên cứu về việc sử dụng palladium làm chất xúc tác để tạo nên các hợp chất hữu cơ. Điều nào sau đây không đúng khi nói về chất xúc tác palladium?', 'Nó giúp làm tăng sản phẩm phụ', 'Nó không bị thay đổi cả về lượng và chất sau phản ứng', 'Nó làm tăng tốc độ hình thành các phân tử hữu cơ', 'Nó giúp tạo các phân tử hữu cơ', 'A'),
(1, 'Quá trình đốt cháy nhiên liệu trong ô tô sinh ra nhiều khí như SO2, CO, NO. Người ta dùng “bộ chuyển đổi xúc tác” trong hệ thống xả khí để tạo điều kiện cho phản ứng: 2CO(g) + 2NO(g) → 2CO2(g) + N2(g). Phát biểu nào sau đây không đúng?', 'Khí CO2 là một trong những nguyên nhân chính gây hiệu ứng nhà kính', 'Trong phản ứng trên, chất bị khử là CO, chất bị oxi hóa là NO', 'Khí NO sinh ra trong động cơ ô tô là do phản ứng của N2 với O2 ở nhiệt độ cao', 'Phản ứng trên chuyển các khí độc hại như CO, NO thành khí ít độc hại hơn là CO2, N2 nên có lợi cho môi trường', 'B'),
(1, 'Phương pháp chung để điều chế các kim loại Na, Ca, Al trong công nghiệp là:', 'Thủy luyện', 'Điện phân dung dịch', 'Nhiệt luyện', 'Điện phân nóng chảy', 'D'),
(1, 'Hợp chất nào sau đây, nguyên tố sắt (Fe) chỉ có số oxi hóa +2?', 'Fe3O4', 'Fe2S3', 'Fe(OH)3', 'FeO', 'D'),
(1, 'Cho các phát biểu sau về nước cứng: (a) Nước cứng là nước chứa nhiều cation Ca2+ và Mg2+. (b) Nước chứa ít hoặc không chứa các cation Ba2+ và Fe2+ được gọi là nước mềm. (c) Soda, sodium chloride và sodium phosphate đều có tác dụng làm mềm nước cứng. (d) Phương pháp trao đổi ion chỉ làm giảm được tính cứng tạm thời của nước. (e) Sự đóng cặn calcium carbonate trong dụng cụ đun nước hay trong đường ống dẫn nước là dấu hiệu của việc sử dụng nước cứng. Số phát biểu không đúng là:', '3', '2', '4', '5', 'A'),
(1, 'Vôi đen (quặng dolomite nghiền nhỏ) được sử dụng trong luyện kim, phân bón và nuôi trồng thủy sản. Thành phần chính của vôi đen là:', 'CaSO4·2H2O', 'CaCO3·MgCO3', 'CaO', '3Ca3(PO4)2·CaF2', 'B'),
(1, 'Benzyl acetate là este có mùi thơm của hoa nhài. Số nguyên tử hydrogen trong công thức của benzyl acetate là:', '10', '2', '8', '12', 'A'),
(1, 'Có 2 kim loại X, Y thỏa mãn: với dung dịch HCl cả hai đều tác dụng; với dung dịch HNO3 đặc, nguội, kim loại X bị thụ động hóa, kim loại Y vẫn tác dụng. Các kim loại X, Y lần lượt là:', 'Fe, Mg', 'Mg, Fe', 'Fe, Al', 'Fe, Cr', 'A'),
(1, 'Phát biểu nào sau đây về phức chất [Ag(NH3)2]+ là đúng?', 'Liên kết giữa NH3 với ion Ag+ trong phức chất là liên kết cho – nhận', 'Phức chất trên thuộc loại phức chất không mang điện', 'Phức chất này được tạo ra bằng phản ứng trực tiếp giữa Ag với NH3', 'Số phối tử trong cầu nội của phức chất trên là 3', 'A'),
(1, 'Chất nào dưới đây không tan trong nước nhưng tan được trong dung dịch Schweizer?', 'Cellulose', 'Saccharose', 'Maltose', 'Fructose', 'A'),
(1, 'Các gốc α-glucose trong phân tử tinh bột tạo dạng mạch amylose không nhánh liên kết với nhau bởi liên kết:', 'β-1,2-glycoside', 'α-1,3-glycoside', 'α-1,4-glycoside', 'α-1,6-glycoside', 'C'),
(1, 'Thủy phân hoàn toàn triglyceride X trong dung dịch NaOH thu được C17H35COONa và C3H5(OH)3. Công thức của X là:', '(C17H33COO)3C3H5', '(C17H35COO)3C3H5', '(C17H31COO)3C3H5', '(C15H31COO)3C3H5', 'B');

INSERT INTO de_thi (id, ten_de, nguon, mo_ta, ma_de, nam)
VALUES
  (2, 'Đề số 1 – Tổng hợp kiến thức Hóa học cơ bản', 'ChemLearn', '10 câu phủ kiến thức đại cương và tính toán nhanh.', 'D1', 2025),
  (3, 'Đề số 2 – Bài tập tính toán Hóa học', 'ChemLearn', '10 bài tập stoichiometry và pha trộn dung dịch.', 'D2', 2025),
  (4, 'Đề số 3 – Hóa học đại cương & cấu tạo chất', 'ChemLearn', 'Ôn cấu hình e, liên kết và bảng tuần hoàn.', 'D3', 2025),
  (5, 'Đề số 4 – Oxi hóa - khử & điện phân', 'ChemLearn', 'Các câu hỏi redox, điện phân và nhiệt hóa.', 'D4', 2025)
ON DUPLICATE KEY UPDATE
  ten_de = VALUES(ten_de),
  nguon = VALUES(nguon),
  mo_ta = VALUES(mo_ta),
  ma_de = VALUES(ma_de),
  nam = VALUES(nam);

DELETE FROM cau_hoi_de_thi WHERE de_thi_id IN (2, 3, 4, 5);

INSERT INTO cau_hoi_de_thi (de_thi_id, noi_dung, dapan_a, dapan_b, dapan_c, dapan_d, dap_an) VALUES
-- Đề số 1 (id=2)
(2, 'Nguyên tử của nguyên tố X có tổng số hạt proton, neutron, electron là 52. Số hạt mang điện nhiều hơn số hạt không mang điện là 16. Số khối (A) của X là bao nhiêu?', '16', '17', '35', '36', 'C'),
(2, 'Nguyên tố Y thuộc chu kì 3, nhóm VA trong bảng tuần hoàn. Số electron hóa trị của Y là bao nhiêu?', '3', '5', '8', '15', 'B'),
(2, 'Chất nào sau đây không phản ứng với dung dịch HCl loãng?', 'Fe2O3', 'Cu(OH)2', 'Mg', 'Ag', 'D'),
(2, 'Trong phản ứng oxi hóa - khử: Fe + HNO3 → Fe(NO3)3 + NO + H2O. Tổng hệ số tối giản của Fe và HNO3 là bao nhiêu?', '4', '5', '6', '7', 'B'),
(2, 'Muối nào sau đây khi tan trong nước tạo dung dịch có môi trường kiềm (pH > 7)?', 'NaCl', 'NH4Cl', 'K2SO4', 'CH3COONa', 'D'),
(2, 'Cho 5,6 gam Fe tác dụng hết với dung dịch HCl dư. Thể tích khí H2 thoát ra (đktc) là bao nhiêu?', '1,12 lít', '2,24 lít', '3,36 lít', '4,48 lít', 'B'),
(2, 'Công thức phân tử của alkane đơn giản nhất là gì?', 'C2H4', 'C2H6', 'CH4', 'C3H8', 'C'),
(2, 'Oxit nào sau đây là oxit lưỡng tính?', 'SO2', 'Na2O', 'CO2', 'Al2O3', 'D'),
(2, 'Hiện tượng nào sau đây là biến đổi hóa học?', 'Nước đá tan chảy', 'Thủy tinh bị vỡ', 'Nước bay hơi', 'Đốt cháy cồn', 'D'),
(2, 'Hòa tan 11,7 gam NaCl vào nước được 500 ml dung dịch. Nồng độ mol là bao nhiêu?', '0,1 M', '0,2 M', '0,4 M', '0,5 M', 'C'),
-- Đề số 2 (id=3)
(3, 'Hòa tan 6,2 gam Na2O vào nước thu được 200 ml dung dịch. Nồng độ mol của NaOH là bao nhiêu?', '0,5 M', '1,0 M', '1,5 M', '2,0 M', 'B'),
(3, 'Cho 4,8 gam Mg tác dụng với dung dịch H2SO4 loãng dư. Khối lượng muối MgSO4 thu được là bao nhiêu?', '12,0 gam', '24,0 gam', '28,8 gam', '16,8 gam', 'B'),
(3, 'Cho 0,2 mol Ba(OH)2 tác dụng với 0,3 mol CO2. Khối lượng kết tủa thu được là bao nhiêu?', '9,85 gam', '19,7 gam', '29,55 gam', '39,4 gam', 'B'),
(3, 'Cần lấy bao nhiêu ml dung dịch NaOH 1M để trung hòa 100 ml dung dịch H2SO4 0,5M?', '50 ml', '100 ml', '200 ml', '250 ml', 'B'),
(3, 'Đốt cháy hoàn toàn 4,4 gam C3H8. Thể tích khí CO2 (đktc) thu được là bao nhiêu?', '2,24 lít', '3,36 lít', '6,72 lít', '4,48 lít', 'C'),
(3, 'Trộn 100 ml dung dịch HCl 0,1M với 100 ml dung dịch NaOH 0,1M. pH của dung dịch sau phản ứng là:', 'pH = 1', 'pH = 7', 'pH = 13', 'pH không xác định', 'B'),
(3, 'Hòa tan 27 gam Al trong dung dịch H2SO4 đặc, nóng, dư thu được 33,6 lít khí SO2 (đktc). Hiệu suất phản ứng là bao nhiêu?', '66,67%', '75,00%', '100%', '80,00%', 'C'),
(3, 'Cho m gam Cu tác dụng với HNO3 dư thu được 4,48 lít khí NO (đktc). Giá trị của m là:', '6,4 gam', '12,8 gam', '19,2 gam', '25,6 gam', 'C'),
(3, 'Nhiệt phân hoàn toàn 10 gam CaCO3, sau phản ứng thu được 4,48 gam chất rắn. Hiệu suất phản ứng là bao nhiêu?', '44,8%', '50%', '80%', '60%', 'C'),
(3, 'Dung dịch X chứa 0,1 mol Na+; 0,05 mol Mg2+; 0,15 mol Cl- và y mol SO4 2-. Giá trị của y là:', '0,025', '0,05', '0,1', '0,15', 'B'),
-- Đề số 3 (id=4)
(4, 'Nguyên tử của nguyên tố S (Z=16) có cấu hình electron là:', '1s2 2s2 2p6 3s2 3p2', '1s2 2s2 2p6 3s2 3p4', '1s2 2s2 2p6 3s2 3p6', '1s2 2s2 2p6 3s2 3p5', 'B'),
(4, 'Nguyên tố nào sau đây có tính phi kim mạnh nhất?', 'O', 'N', 'Cl', 'F', 'D'),
(4, 'Trong hợp chất NH3, loại liên kết hóa học chủ yếu là:', 'Liên kết ion', 'Liên kết cộng hóa trị phân cực', 'Liên kết cộng hóa trị không phân cực', 'Liên kết kim loại', 'B'),
(4, 'Nguyên tử K (Z=19) thuộc chu kì và nhóm nào trong bảng tuần hoàn?', 'Chu kì 3, nhóm IA', 'Chu kì 4, nhóm IA', 'Chu kì 3, nhóm VIIA', 'Chu kì 4, nhóm IIA', 'B'),
(4, 'Trong phản ứng Na+ + Cl- → NaCl, nguyên tử Na đã:', 'Nhận 1e-, thể hiện tính oxi hóa', 'Nhường 1e-, thể hiện tính khử', 'Nhận 1e-, thể hiện tính khử', 'Nhường 1e-, thể hiện tính oxi hóa', 'B'),
(4, 'Ion nào sau đây có cùng cấu hình electron của khí hiếm Ne (Z=10)?', 'Cl- (Z=17)', 'Ca2+ (Z=20)', 'S2- (Z=16)', 'Na+ (Z=11)', 'D'),
(4, 'Hạt nhân nguyên tử được cấu tạo từ các hạt:', 'Proton và electron', 'Neutron và electron', 'Proton và neutron', 'Proton, neutron và electron', 'C'),
(4, 'Đại lượng đặc trưng cho khả năng hút electron của một nguyên tử khi tạo liên kết là:', 'Năng lượng ion hóa', 'Ái lực electron', 'Độ âm điện', 'Bán kính nguyên tử', 'C'),
(4, 'Đồng vị là những nguyên tử của cùng một nguyên tố, có:', 'Cùng số neutron, khác số proton', 'Khác số proton, khác số neutron', 'Cùng số proton, khác số neutron', 'Cùng số proton, cùng số neutron', 'C'),
(4, 'Nguyên tử X có 4 lớp electron, lớp ngoài cùng có 5 electron. X thuộc:', 'Chu kì 3, nhóm VA', 'Chu kì 4, nhóm IIA', 'Chu kì 4, nhóm VA', 'Chu kì 3, nhóm IVA', 'C'),
-- Đề số 4 (id=5)
(5, 'Trong phản ứng: Zn + 2HCl → ZnCl2 + H2. Chất oxi hóa là:', 'Zn', 'HCl', 'ZnCl2', 'H2', 'B'),
(5, 'Số oxi hóa của Mn trong KMnO4 là:', '+4', '+6', '+7', '+2', 'C'),
(5, 'Trong phản ứng oxi hóa - khử, chất khử là chất:', 'Nhường electron và chứa nguyên tố có số oxi hóa tăng', 'Nhận electron và chứa nguyên tố có số oxi hóa tăng', 'Nhường electron và chứa nguyên tố có số oxi hóa giảm', 'Nhận electron và chứa nguyên tố có số oxi hóa giảm', 'A'),
(5, 'Khi điện phân dung dịch CuSO4 với điện cực trơ, tại cathode xảy ra quá trình gì?', 'Sự oxi hóa của Cu2+', 'Sự khử của Cu2+', 'Sự oxi hóa của H2O', 'Sự khử của SO4 2-', 'B'),
(5, 'Cho phản ứng: Fe3O4 + HNO3 → Fe(NO3)3 + NO + H2O. Tỷ lệ số mol giữa chất khử (Fe3O4) và chất oxi hóa (HNO3 tạo NO) là:', '1:1', '3:1', '1:9', '3:2', 'C'),
(5, 'Trong phản ứng Cl2 + 2NaOH → NaCl + NaClO + H2O, Cl2 đóng vai trò:', 'Chất khử', 'Chất oxi hóa', 'Chất tự oxi hóa - tự khử', 'Chất tạo môi trường', 'C'),
(5, 'Sự biến đổi nào sau đây không phải là phản ứng oxi hóa - khử?', 'Fe3+ → Fe2+', 'S2- → S0', 'H2S + KOH → KHS + H2O', 'Cu → Cu2+', 'C'),
(5, 'Cho 0,1 mol Al tác dụng với HNO3 đặc, nóng, dư. Số mol electron trao đổi là:', '0,1 mol', '0,2 mol', '0,3 mol', '0,4 mol', 'C'),
(5, 'Phản ứng nào sau đây là phản ứng thu nhiệt?', 'Phản ứng đốt cháy CH4', 'Phản ứng trung hòa axit - bazơ', 'Phản ứng quang hợp', 'Phản ứng Na tác dụng với nước', 'C'),
(5, 'Phương pháp chính dùng để điều chế Na trong công nghiệp là:', 'Điện phân dung dịch NaCl', 'Điện phân NaCl nóng chảy', 'Dùng K đẩy Na ra khỏi muối', 'Điện phân dung dịch NaOH', 'B');

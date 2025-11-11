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

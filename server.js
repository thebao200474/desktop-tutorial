require('dotenv').config();
const fs = require('fs');
const path = require('path');
const express = require('express');
const Database = require('better-sqlite3');
const SibApiV3Sdk = require('sib-api-v3-sdk');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');

const app = express();
const PORT = process.env.PORT || 3000;
const JWT_SECRET = process.env.JWT_SECRET || 'dev-secret-key';

app.use(express.json());
app.use(express.static(path.join(__dirname, 'public')));

const dbPath = path.join(__dirname, 'db', 'library.db');
const db = new Database(dbPath);

db.pragma('journal_mode = WAL');
db.exec(fs.readFileSync(path.join(__dirname, 'db', 'init.sql'), 'utf-8'));

function ensureSchemaCompatibility() {
  const docgiaCols = db.prepare("PRAGMA table_info(Docgia)").all();
  if (!docgiaCols.some((x) => x.name === 'Password')) db.exec('ALTER TABLE Docgia ADD COLUMN Password TEXT');
  if (!docgiaCols.some((x) => x.name === 'PasswordHash')) db.exec('ALTER TABLE Docgia ADD COLUMN PasswordHash TEXT');
  if (!docgiaCols.some((x) => x.name === 'Email')) db.exec('ALTER TABLE Docgia ADD COLUMN Email TEXT');

  const otpCols = db.prepare("PRAGMA table_info(OTPToken)").all();
  if (!otpCols.some((x) => x.name === 'purpose')) db.exec("ALTER TABLE OTPToken ADD COLUMN purpose TEXT DEFAULT 'login'");
  if (!otpCols.some((x) => x.name === 'isUsed')) db.exec('ALTER TABLE OTPToken ADD COLUMN isUsed INTEGER DEFAULT 0');
  if (!otpCols.some((x) => x.name === 'createdAt')) db.exec('ALTER TABLE OTPToken ADD COLUMN createdAt TEXT');

  db.exec("UPDATE OTPToken SET createdAt = CURRENT_TIMESTAMP WHERE createdAt IS NULL");
}

ensureSchemaCompatibility();

function seedData() {
  const hasPublisher = db.prepare('SELECT COUNT(*) AS count FROM NhaXuatBan').get().count;
  if (hasPublisher > 0) return;

  const adminPassword = bcrypt.hashSync('admin123', 10);
  const readerPassword = bcrypt.hashSync('reader123', 10);

  db.prepare('INSERT INTO NhaXuatBan(MaNXB, TenNXB, DiaChi) VALUES (?, ?, ?)').run('NXB01', 'NXB Tre', 'TP.HCM');
  db.prepare('INSERT INTO NhaXuatBan(MaNXB, TenNXB, DiaChi) VALUES (?, ?, ?)').run('NXB02', 'Nha Nam', 'Ha Noi');

  const books = [
    ['S001', 'Đắc nhân tâm', 90000, 12, 2019, 'NXB01', 'Dale Carnegie', 'Cuốn sách nổi tiếng về nghệ thuật giao tiếp, ứng xử và xây dựng mối quan hệ hiệu quả.', 'Kỹ năng sống', '/images/books/dac-nhan-tam.svg'],
    ['S002', 'Nhà giả kim', 79000, 8, 2020, 'NXB02', 'Paulo Coelho', 'Hành trình theo đuổi ước mơ và lắng nghe trái tim qua câu chuyện đầy tính biểu tượng.', 'Tiểu thuyết', '/images/books/nha-gia-kim.svg'],
    ['S003', 'Sapiens - Lược sử loài người', 180000, 6, 2021, 'NXB02', 'Yuval Noah Harari', 'Tác phẩm khái quát lịch sử phát triển của loài người từ thời nguyên thủy đến hiện đại.', 'Khoa học', '/images/books/sapiens.svg'],
    ['S004', 'Hạt giống tâm hồn', 88000, 15, 2018, 'NXB01', 'Nhiều tác giả', 'Tuyển tập các câu chuyện truyền cảm hứng về nghị lực, niềm tin và lòng biết ơn.', 'Kỹ năng sống', '/images/books/hat-giong-tam-hon.svg'],
    ['S005', 'Tôi tài giỏi, bạn cũng thế!', 99000, 10, 2022, 'NXB01', 'Adam Khoo', 'Phương pháp học tập, phát triển tư duy và tạo động lực cho học sinh, sinh viên.', 'Kỹ năng sống', '/images/books/toi-tai-gioi-ban-cung-the.svg'],
    ['S006', 'Muôn kiếp nhân sinh', 115000, 7, 2020, 'NXB01', 'Nguyên Phong', 'Tác phẩm kết hợp yếu tố chiêm nghiệm, tâm linh và góc nhìn về nhân quả trong cuộc sống.', 'Tâm lý', '/images/books/muon-kiep-nhan-sinh.svg'],
    ['S007', 'Tuổi trẻ đáng giá bao nhiêu', 86000, 11, 2021, 'NXB02', 'Rosie Nguyễn', 'Những chia sẻ gần gũi về học tập, trải nghiệm, trưởng thành và giá trị của tuổi trẻ.', 'Kỹ năng sống', '/images/books/tuoi-tre-dang-gia-bao-nhieu.svg'],
    ['S008', 'Thinking, Fast and Slow', 210000, 5, 2017, 'NXB02', 'Daniel Kahneman', 'Phân tích cách con người ra quyết định thông qua hai hệ thống tư duy nhanh và chậm.', 'Kinh tế', '/images/books/thinking-fast-and-slow.svg'],
    ['S009', 'Lược sử thời gian', 165000, 4, 2016, 'NXB01', 'Stephen Hawking', 'Cuốn sách khoa học phổ thông kinh điển giải thích vũ trụ, thời gian và các bí ẩn vật lý.', 'Khoa học', '/images/books/luoc-su-thoi-gian.svg'],
    ['S010', 'Đi tìm lẽ sống', 129000, 9, 2022, 'NXB01', 'Viktor E. Frankl', 'Tác phẩm nổi tiếng về ý nghĩa cuộc sống, nghị lực và sức mạnh tinh thần của con người.', 'Tâm lý', '/images/books/di-tim-le-song.svg']
  ];

  const insertBook = db.prepare(`
    INSERT INTO Sach(MaSach, TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  `);

  books.forEach((book) => insertBook.run(...book));

  db.prepare(`
    INSERT INTO Docgia(MaDocGia, HoLot, Ten, NgaySinh, Phai, DiaChi, DienThoai, Email, Password, PasswordHash)
    VALUES ('DG001', 'Nguyen Van', 'An', '2001-05-10', 'Nam', 'Can Tho', '0900000001', 'docgia1@example.com', ?, ?)
  `).run(readerPassword, readerPassword);

  db.prepare(`
    INSERT INTO NhanVien(MSNV, HoTenNV, Password, ChucVu, DiaChi, SoDienThoai, Email)
    VALUES ('NV001', 'Tran Thi Admin', ?, 'Quan tri', 'TP.HCM', '0900000099', 'admin@example.com')
  `).run(adminPassword);
}

seedData();

function authMiddleware(req, res, next) {
  const header = req.headers.authorization;
  if (!header?.startsWith('Bearer ')) {
    return res.status(401).json({ message: 'Thiếu token.' });
  }

  try {
    const token = header.replace('Bearer ', '');
    req.user = jwt.verify(token, JWT_SECRET);
    return next();
  } catch (error) {
    return res.status(401).json({ message: 'Token không hợp lệ.' });
  }
}

function adminOnly(req, res, next) {
  if (req.user.role !== 'admin') {
    return res.status(403).json({ message: 'Chỉ quản trị viên được truy cập.' });
  }
  return next();
}

const brevoClient = SibApiV3Sdk.ApiClient.instance;
const brevoApiKey = brevoClient.authentications['api-key'];
if (process.env.BREVO_API_KEY) {
  brevoApiKey.apiKey = process.env.BREVO_API_KEY;
}
const brevoEmailApi = new SibApiV3Sdk.TransactionalEmailsApi();

function getMissingBrevoEnv() {
  return ['BREVO_API_KEY', 'BREVO_SENDER_EMAIL'].filter((name) => !process.env[name]);
}

const missingBrevoAtStartup = getMissingBrevoEnv();
if (missingBrevoAtStartup.length > 0) {
  console.warn(`[BREVO CONFIG] Thiếu biến môi trường: ${missingBrevoAtStartup.join(', ')}`);
}

async function sendOTPEmail(email, otp, purpose = 'register') {
  const missingEnv = getMissingBrevoEnv();
  if (missingEnv.length > 0) {
    throw new Error(`Thiếu cấu hình Brevo trong file .env: ${missingEnv.join(', ')}`);
  }

  const subject = purpose === 'register' ? 'Mã OTP đăng ký BookHub' : 'Mã OTP đăng nhập BookHub';
  const sendSmtpEmail = {
    sender: {
      email: process.env.BREVO_SENDER_EMAIL,
      name: process.env.BREVO_SENDER_NAME || 'BookHub'
    },
    to: [{ email }],
    subject,
    htmlContent: `
      <h2>BookHub</h2>
      <p>Mã OTP của bạn là:</p>
      <h1 style="color:#2e7d32;">${otp}</h1>
      <p>OTP có hiệu lực trong ${process.env.OTP_EXPIRE_MINUTES || 5} phút.</p>
    `
  };

  console.log(`[OTP][${purpose}] Đang gửi tới: ${email}`);
  try {
    const response = await brevoEmailApi.sendTransacEmail(sendSmtpEmail);
    console.log(`[OTP][${purpose}] Gửi thành công tới ${email}. messageId=${response?.messageId || 'n/a'}`);
    return response;
  } catch (err) {
    console.error('BREVO ERROR:', err?.response?.body || err);
    throw err;
  }
}

function generateDocGiaId() {
  const last = db.prepare("SELECT MaDocGia FROM Docgia ORDER BY MaDocGia DESC LIMIT 1").get();
  const num = last ? Number(String(last.MaDocGia).replace('DG', '')) + 1 : 1;
  return `DG${String(num).padStart(3, '0')}`;
}

const otpCooldown = new Map();
const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

app.post('/api/auth/send-otp-register', async (req, res) => {
  const email = String(req.body.email || '').trim().toLowerCase();
  if (!EMAIL_REGEX.test(email)) {
    return res.status(400).json({ success: false, message: 'Email không hợp lệ.' });
  }

  const exists = db.prepare('SELECT 1 FROM Docgia WHERE lower(Email) = ?').get(email);
  if (exists) {
    return res.status(409).json({ success: false, message: 'Email đã tồn tại.' });
  }

  const cooldown = otpCooldown.get(email) || 0;
  const now = Date.now();
  if (cooldown > now) {
    return res.status(429).json({ success: false, message: `Vui lòng thử lại sau ${Math.ceil((cooldown - now) / 1000)} giây.` });
  }

  const otp = `${Math.floor(100000 + Math.random() * 900000)}`;
  const otpExpireMinutes = Number(process.env.OTP_EXPIRE_MINUTES || 5);
  const expiresAt = Date.now() + otpExpireMinutes * 60 * 1000;
  const createdAt = new Date().toISOString();

  try {
    await sendOTPEmail(email, otp, 'register');
    db.prepare('UPDATE OTPToken SET isUsed = 1 WHERE email = ? AND purpose = ?').run(email, 'register');
    db.prepare('INSERT INTO OTPToken(email, otp, role, purpose, expiresAt, isUsed, createdAt) VALUES (?, ?, ?, ?, ?, 0, ?)').run(email, otp, 'reader', 'register', expiresAt, createdAt);
    otpCooldown.set(email, now + 60 * 1000);
    return res.json({ success: true, message: 'Đã gửi OTP về email' });
  } catch (err) {
    console.error('BREVO ERROR:', err?.response?.body || err);
    return res.status(500).json({ success: false, message: err?.message || 'Gửi OTP thất bại' });
  }
});


app.get('/api/test-send-mail', async (req, res) => {
  const targetEmail = String(req.query.email || process.env.TEST_RECEIVER_EMAIL || '').trim().toLowerCase();
  if (!EMAIL_REGEX.test(targetEmail)) {
    return res.status(400).json({ success: false, message: 'Thiếu email test hợp lệ (query ?email=...) hoặc TEST_RECEIVER_EMAIL.' });
  }

  const otp = '123456';
  try {
    await sendOTPEmail(targetEmail, otp, 'register');
    return res.json({ success: true, message: `Đã gửi mail test tới ${targetEmail}` });
  } catch (err) {
    console.error('BREVO ERROR:', err?.response?.body || err);
    return res.status(500).json({ success: false, message: 'Gửi mail test thất bại' });
  }
});

app.post('/api/auth/register', (req, res) => {
  const {
    hoLot = '',
    ten = '',
    ngaySinh = null,
    phai = '',
    diaChi = '',
    dienThoai = '',
    email = '',
    password = '',
    confirmPassword = '',
    otp = ''
  } = req.body;

  const normalizedEmail = String(email).trim().toLowerCase();
  if (!hoLot || !ten || !EMAIL_REGEX.test(normalizedEmail) || !dienThoai || !password || !confirmPassword || !otp) {
    return res.status(400).json({ success: false, message: 'Thiếu trường bắt buộc hoặc email không hợp lệ.' });
  }
  if (!/^\d{9,11}$/.test(String(dienThoai))) {
    return res.status(400).json({ success: false, message: 'Số điện thoại không hợp lệ.' });
  }
  if (!['Nam', 'Nữ', 'Khác'].includes(phai)) {
    return res.status(400).json({ success: false, message: 'Phái không hợp lệ.' });
  }
  if (password.length < 6) {
    return res.status(400).json({ success: false, message: 'Mật khẩu tối thiểu 6 ký tự.' });
  }
  if (password !== confirmPassword) {
    return res.status(400).json({ success: false, message: 'Mật khẩu xác nhận không khớp.' });
  }

  const existed = db.prepare('SELECT 1 FROM Docgia WHERE lower(Email) = ?').get(normalizedEmail);
  if (existed) {
    return res.status(409).json({ success: false, message: 'Email đã tồn tại.' });
  }

  const otpRow = db
    .prepare('SELECT * FROM OTPToken WHERE email = ? AND purpose = ? AND isUsed = 0 ORDER BY id DESC LIMIT 1')
    .get(normalizedEmail, 'register');

  if (!otpRow || otpRow.otp !== String(otp) || Number(otpRow.expiresAt) < Date.now()) {
    return res.status(400).json({ success: false, message: 'OTP không đúng hoặc đã hết hạn.' });
  }

  const MaDocGia = generateDocGiaId();
  const hash = bcrypt.hashSync(password, 10);

  db.prepare(
    `INSERT INTO Docgia(MaDocGia, HoLot, Ten, NgaySinh, Phai, DiaChi, DienThoai, Email, Password, PasswordHash)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
  ).run(MaDocGia, hoLot, ten, ngaySinh, phai, diaChi, dienThoai, normalizedEmail, hash, hash);

  db.prepare('UPDATE OTPToken SET isUsed = 1 WHERE id = ?').run(otpRow.id);

  return res.json({ success: true, message: 'Đăng ký tài khoản thành công' });
});

app.post('/api/auth/login', (req, res) => {
  const { email, password } = req.body;
  const user = db.prepare('SELECT MaDocGia, HoLot, Ten, Email, Password, PasswordHash FROM Docgia WHERE lower(Email) = ?').get(String(email || '').toLowerCase());
  const storedHash = user?.PasswordHash || user?.Password;
  if (!user || !storedHash || !bcrypt.compareSync(password, storedHash)) {
    return res.status(401).json({ message: 'Email hoặc mật khẩu không đúng.' });
  }

  const token = jwt.sign({ sub: user.MaDocGia, role: 'reader', email: user.Email }, JWT_SECRET, { expiresIn: '8h' });
  return res.json({
    message: 'Đăng nhập thành công.',
    token,
    profile: { id: user.MaDocGia, name: `${user.HoLot} ${user.Ten}`.trim(), Email: user.Email, role: 'reader' }
  });
});

app.post('/api/auth/send-otp', async (req, res) => {
  const { email, role = 'reader' } = req.body;
  if (!email) {
    return res.status(400).json({ success: false, message: 'Email là bắt buộc.' });
  }

  const userExists =
    role === 'admin'
      ? db.prepare('SELECT 1 FROM NhanVien WHERE Email = ?').get(email)
      : db.prepare('SELECT 1 FROM Docgia WHERE Email = ?').get(email);

  if (!userExists) {
    return res.status(404).json({ success: false, message: 'Không tìm thấy tài khoản với email này.' });
  }

  const otp = `${Math.floor(100000 + Math.random() * 900000)}`;
  const otpExpireMinutes = Number(process.env.OTP_EXPIRE_MINUTES || 5);
  const expiresAt = Date.now() + otpExpireMinutes * 60 * 1000;
  const createdAt = new Date().toISOString();

  try {
    await sendOTPEmail(email, otp, 'login');
    db.prepare('UPDATE OTPToken SET isUsed = 1 WHERE email = ? AND role = ?').run(email, role);
    db.prepare('INSERT INTO OTPToken(email, otp, role, purpose, expiresAt, isUsed, createdAt) VALUES (?, ?, ?, ?, ?, 0, ?)').run(email, otp, role, 'login', expiresAt, createdAt);
    return res.json({ success: true, message: 'Đã gửi OTP thành công.' });
  } catch (err) {
    console.error('BREVO ERROR:', err?.response?.body || err);
    return res.status(500).json({ success: false, message: err?.message || 'Gửi OTP thất bại' });
  }
});

app.post('/api/auth/verify-otp', (req, res) => {
  const { email, otp, role = 'reader' } = req.body;
  const tokenRow = db
    .prepare('SELECT * FROM OTPToken WHERE email = ? AND role = ? AND purpose = ? AND isUsed = 0 ORDER BY id DESC LIMIT 1')
    .get(email, role, 'login');

  if (!tokenRow) {
    return res.status(400).json({ message: 'Không tìm thấy OTP, vui lòng yêu cầu lại.' });
  }

  if (tokenRow.otp !== otp || tokenRow.expiresAt < Date.now()) {
    return res.status(400).json({ message: 'OTP không đúng hoặc đã hết hạn.' });
  }

  const profile =
    role === 'admin'
      ? db.prepare('SELECT MSNV AS id, HoTenNV AS name, Email FROM NhanVien WHERE Email = ?').get(email)
      : db.prepare("SELECT MaDocGia AS id, HoLot || ' ' || Ten AS name, Email FROM Docgia WHERE Email = ?").get(email);

  db.prepare('UPDATE OTPToken SET isUsed = 1 WHERE id = ?').run(tokenRow.id);

  const token = jwt.sign({ sub: profile.id, role, email }, JWT_SECRET, { expiresIn: '8h' });
  return res.json({ message: 'Xác thực thành công.', token, profile: { ...profile, role } });
});

app.get('/api/search-books', (req, res) => {
  const q = req.query.q || '';
  const books = db
    .prepare(
      `SELECT s.*, n.TenNXB
       FROM Sach s
       LEFT JOIN NhaXuatBan n ON n.MaNXB = s.MaNXB
       WHERE s.TenSach LIKE ? OR s.NguonGoc LIKE ? OR s.TheLoai LIKE ?
       ORDER BY s.TenSach`
    )
    .all(`%${q}%`, `%${q}%`, `%${q}%`);
  res.json({ books });
});


app.get('/api/books', (req, res) => {
  const {
    search = '',
    genre = 'all',
    author = 'all',
    minRating = 0,
    yearFrom = 0,
    sort = 'newest',
    page = 1,
    pageSize = 10
  } = req.query;

  const filters = ['1=1'];
  const params = [];

  if (search) {
    filters.push('(s.TenSach LIKE ? OR s.NguonGoc LIKE ? OR s.TheLoai LIKE ?)');
    params.push(`%${search}%`, `%${search}%`, `%${search}%`);
  }

  if (genre !== 'all') {
    filters.push('s.TheLoai = ?');
    params.push(genre);
  }

  if (author !== 'all') {
    filters.push('s.NguonGoc = ?');
    params.push(author);
  }

  if (Number(yearFrom) > 0) {
    filters.push('s.NamXuatBan >= ?');
    params.push(Number(yearFrom));
  }

  if (Number(minRating) > 0) {
    filters.push('COALESCE(avgRating, 0) >= ?');
    params.push(Number(minRating));
  }

  const sortMap = {
    newest: 's.NamXuatBan DESC',
    rating: 'avgRating DESC',
    titleAZ: 's.TenSach ASC',
    quantityDesc: 's.SoQuyen DESC',
    priceAsc: 's.DonGia ASC',
    priceDesc: 's.DonGia DESC'
  };

  const orderBy = sortMap[sort] || sortMap.newest;
  const limit = Math.max(1, Number(pageSize) || 10);
  const offset = (Math.max(1, Number(page) || 1) - 1) * limit;

  const baseQuery = `
    FROM (
      SELECT s.*,
             COALESCE(AVG(r.Diem), 0) AS avgRating,
             COUNT(r.id) AS ratingCount
      FROM Sach s
      LEFT JOIN DanhGiaSach r ON r.MaSach = s.MaSach
      GROUP BY s.MaSach
    ) s
    WHERE ${filters.join(' AND ')}
  `;

  const countRow = db.prepare(`SELECT COUNT(*) AS total ${baseQuery}`).get(...params);
  const books = db
    .prepare(`
      SELECT s.*
      ${baseQuery}
      ORDER BY ${orderBy}
      LIMIT ? OFFSET ?
    `)
    .all(...params, limit, offset);

  res.json({
    books,
    pagination: {
      total: countRow.total,
      page: Math.max(1, Number(page) || 1),
      pageSize: limit,
      totalPages: Math.ceil(countRow.total / limit)
    }
  });
});

app.get('/api/books/:id', (req, res) => {
  const book = db
    .prepare(
      `SELECT s.*, n.TenNXB,
              COALESCE(AVG(r.Diem), 0) AS avgRating,
              COUNT(r.id) AS totalReviews
       FROM Sach s
       LEFT JOIN NhaXuatBan n ON n.MaNXB = s.MaNXB
       LEFT JOIN DanhGiaSach r ON r.MaSach = s.MaSach
       WHERE s.MaSach = ?`
    )
    .get(req.params.id);

  if (!book) return res.status(404).json({ message: 'Không tìm thấy sách.' });
  return res.json({ book });
});

app.get('/api/books/:id/reviews', (req, res) => {
  const reviews = db
    .prepare(
      `SELECT r.*, d.HoLot || ' ' || d.Ten AS DocGia
       FROM DanhGiaSach r
       LEFT JOIN Docgia d ON d.MaDocGia = r.MaDocGia
       WHERE r.MaSach = ?
       ORDER BY r.id DESC`
    )
    .all(req.params.id);
  res.json({ reviews });
});

app.post('/api/books/:id/reviews', authMiddleware, (req, res) => {
  const { Diem, BinhLuan = '' } = req.body;
  if (!Diem || Diem < 1 || Diem > 5) {
    return res.status(400).json({ message: 'Điểm đánh giá phải từ 1-5.' });
  }

  db.prepare('INSERT INTO DanhGiaSach(MaDocGia, MaSach, Diem, BinhLuan) VALUES (?, ?, ?, ?)').run(req.user.sub, req.params.id, Diem, BinhLuan);
  return res.json({ message: 'Đã gửi đánh giá.' });
});

app.post('/api/borrow-book', authMiddleware, (req, res) => {
  const { MaSach } = req.body;
  const MaDocGia = req.user.sub;

  const book = db.prepare('SELECT * FROM Sach WHERE MaSach = ?').get(MaSach);
  if (!book || book.SoQuyen <= 0) {
    return res.status(400).json({ message: 'Sách không còn sẵn để mượn.' });
  }

  const now = new Date();
  const dueDate = new Date(now);
  dueDate.setDate(dueDate.getDate() + 14);

  const borrowTx = db.transaction(() => {
    db.prepare('UPDATE Sach SET SoQuyen = SoQuyen - 1 WHERE MaSach = ?').run(MaSach);
    db.prepare(
      `INSERT INTO TheoDoiMuonSach(MaDocGia, MaSach, NgayMuon, HanTra, TrangThai)
       VALUES (?, ?, ?, ?, 'dang_muon')`
    ).run(MaDocGia, MaSach, now.toISOString(), dueDate.toISOString());
  });

  borrowTx();
  return res.json({ message: 'Mượn sách thành công.', dueDate });
});

app.get('/api/my-loans', authMiddleware, (req, res) => {
  const loans = db
    .prepare(
      `SELECT l.*, s.TenSach, s.AnhBia
       FROM TheoDoiMuonSach l
       JOIN Sach s ON s.MaSach = l.MaSach
       WHERE l.MaDocGia = ?
       ORDER BY l.MaMuon DESC`
    )
    .all(req.user.sub);
  res.json({ loans });
});

app.post('/api/chatbot', (req, res) => {
  const { message = '' } = req.body;
  const text = message.toLowerCase();

  if (text.includes('gợi ý') || text.includes('de xuat')) {
    const picks = db.prepare('SELECT TenSach, NguonGoc FROM Sach ORDER BY SoQuyen DESC LIMIT 3').all();
    return res.json({
      reply: `Bạn có thể đọc: ${picks.map((x) => `${x.TenSach} (${x.NguonGoc})`).join(', ')}.`
    });
  }

  if (text.includes('sẵn') || text.includes('co san')) {
    const count = db.prepare('SELECT COUNT(*) AS c FROM Sach WHERE SoQuyen > 0').get().c;
    return res.json({ reply: `Hiện có ${count} đầu sách đang còn để mượn.` });
  }

  return res.json({
    reply:
      'Mình có thể hỗ trợ: tìm sách, gợi ý sách, kiểm tra tình trạng còn sách, hoặc hướng dẫn mượn sách bằng OTP.'
  });
});

app.get('/api/admin/dashboard', authMiddleware, adminOnly, (req, res) => {
  const totalBooks = db.prepare('SELECT COUNT(*) AS c FROM Sach').get().c;
  const totalReaders = db.prepare('SELECT COUNT(*) AS c FROM Docgia').get().c;
  const borrowed = db.prepare("SELECT COUNT(*) AS c FROM TheoDoiMuonSach WHERE TrangThai = 'dang_muon'").get().c;
  const overdue = db
    .prepare("SELECT COUNT(*) AS c FROM TheoDoiMuonSach WHERE TrangThai = 'dang_muon' AND datetime(HanTra) < datetime('now')")
    .get().c;

  res.json({ totalBooks, totalReaders, borrowed, overdue });
});

app.get('/api/admin/books', authMiddleware, adminOnly, (req, res) => {
  const books = db.prepare('SELECT * FROM Sach ORDER BY TenSach').all();
  res.json({ books });
});

app.post('/api/admin/books', authMiddleware, adminOnly, (req, res) => {
  const { MaSach, TenSach, DonGia = 0, SoQuyen = 0, NamXuatBan, MaNXB, NguonGoc, MoTa = '', TheLoai = '', AnhBia = '' } = req.body;
  db.prepare(
    `INSERT INTO Sach(MaSach, TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
  ).run(MaSach, TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia);
  res.json({ message: 'Đã thêm sách.' });
});

app.put('/api/admin/books/:id', authMiddleware, adminOnly, (req, res) => {
  const { TenSach, DonGia = 0, SoQuyen = 0, NamXuatBan, MaNXB, NguonGoc = '', MoTa = '', TheLoai = '', AnhBia = '' } = req.body;
  db.prepare(
    `UPDATE Sach
     SET TenSach = ?, DonGia = ?, SoQuyen = ?, NamXuatBan = ?, MaNXB = ?, NguonGoc = ?, MoTa = ?, TheLoai = ?, AnhBia = ?
     WHERE MaSach = ?`
  ).run(TenSach, DonGia, SoQuyen, NamXuatBan, MaNXB, NguonGoc, MoTa, TheLoai, AnhBia, req.params.id);
  res.json({ message: 'Đã cập nhật sách.' });
});

app.delete('/api/admin/books/:id', authMiddleware, adminOnly, (req, res) => {
  db.prepare('DELETE FROM Sach WHERE MaSach = ?').run(req.params.id);
  res.json({ message: 'Đã xóa sách.' });
});

app.get('/api/admin/users', authMiddleware, adminOnly, (req, res) => {
  const users = db
    .prepare(
      `SELECT d.MaDocGia, d.HoLot, d.Ten, d.Email, d.DienThoai, d.DiaChi,
              CASE
                WHEN EXISTS (
                  SELECT 1 FROM TheoDoiMuonSach l
                  WHERE l.MaDocGia = d.MaDocGia AND l.TrangThai = 'dang_muon'
                ) THEN 'Đang mượn'
                ELSE 'Bình thường'
              END AS TrangThai
       FROM Docgia d
       ORDER BY d.MaDocGia`
    )
    .all();
  res.json({ users });
});

app.get('/api/admin/loans', authMiddleware, adminOnly, (req, res) => {
  const loans = db
    .prepare(
      `SELECT l.MaMuon, d.HoLot || ' ' || d.Ten AS DocGia, s.TenSach, l.NgayMuon, l.HanTra, l.NgayTra, l.TrangThai
       FROM TheoDoiMuonSach l
       JOIN Docgia d ON d.MaDocGia = l.MaDocGia
       JOIN Sach s ON s.MaSach = l.MaSach
       ORDER BY l.MaMuon DESC`
    )
    .all();
  res.json({ loans });
});

app.get('/health', (req, res) => res.json({ ok: true }));

app.listen(PORT, () => {
  console.log(`Server running at http://localhost:${PORT}`);
});

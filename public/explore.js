const MOCK_BOOKS = [
  { MaSach: 'S001', TenSach: 'Đắc nhân tâm', NguonGoc: 'Dale Carnegie', avgRating: 4.6, ratingCount: 2345, TheLoai: 'Kỹ năng sống', NamXuatBan: 2019, SoQuyen: 12, MoTa: 'Cuốn sách nổi tiếng về nghệ thuật giao tiếp, ứng xử và xây dựng mối quan hệ hiệu quả.', AnhBia: '/images/books/dac-nhan-tam.svg' },
  { MaSach: 'S002', TenSach: 'Nhà giả kim', NguonGoc: 'Paulo Coelho', avgRating: 4.7, ratingCount: 1780, TheLoai: 'Tiểu thuyết', NamXuatBan: 2020, SoQuyen: 8, MoTa: 'Hành trình theo đuổi ước mơ và lắng nghe trái tim qua câu chuyện đầy tính biểu tượng.', AnhBia: '/images/books/nha-gia-kim.svg' },
  { MaSach: 'S003', TenSach: 'Sapiens - Lược sử loài người', NguonGoc: 'Yuval Noah Harari', avgRating: 4.8, ratingCount: 3507, TheLoai: 'Khoa học', NamXuatBan: 2021, SoQuyen: 6, MoTa: 'Tác phẩm khái quát lịch sử phát triển của loài người từ thời nguyên thủy đến hiện đại.', AnhBia: '/images/books/sapiens.svg' },
  { MaSach: 'S004', TenSach: 'Hạt giống tâm hồn', NguonGoc: 'Nhiều tác giả', avgRating: 4.5, ratingCount: 1234, TheLoai: 'Kỹ năng sống', NamXuatBan: 2018, SoQuyen: 15, MoTa: 'Tuyển tập các câu chuyện truyền cảm hứng về nghị lực, niềm tin và lòng biết ơn.', AnhBia: '/images/books/hat-giong-tam-hon.svg' },
  { MaSach: 'S005', TenSach: 'Tôi tài giỏi, bạn cũng thế!', NguonGoc: 'Adam Khoo', avgRating: 4.6, ratingCount: 1887, TheLoai: 'Kỹ năng sống', NamXuatBan: 2022, SoQuyen: 10, MoTa: 'Phương pháp học tập, phát triển tư duy và tạo động lực cho học sinh, sinh viên.', AnhBia: '/images/books/toi-tai-gioi-ban-cung-the.svg' },
  { MaSach: 'S006', TenSach: 'Muôn kiếp nhân sinh', NguonGoc: 'Nguyên Phong', avgRating: 4.4, ratingCount: 1642, TheLoai: 'Tâm lý', NamXuatBan: 2020, SoQuyen: 7, MoTa: 'Tác phẩm kết hợp yếu tố chiêm nghiệm, tâm linh và góc nhìn về nhân quả trong cuộc sống.', AnhBia: '/images/books/muon-kiep-nhan-sinh.svg' },
  { MaSach: 'S007', TenSach: 'Tuổi trẻ đáng giá bao nhiêu', NguonGoc: 'Rosie Nguyễn', avgRating: 4.5, ratingCount: 2104, TheLoai: 'Kỹ năng sống', NamXuatBan: 2021, SoQuyen: 11, MoTa: 'Những chia sẻ gần gũi về học tập, trải nghiệm, trưởng thành và giá trị của tuổi trẻ.', AnhBia: '/images/books/tuoi-tre-dang-gia-bao-nhieu.svg' },
  { MaSach: 'S008', TenSach: 'Thinking, Fast and Slow', NguonGoc: 'Daniel Kahneman', avgRating: 4.7, ratingCount: 1420, TheLoai: 'Kinh tế', NamXuatBan: 2017, SoQuyen: 5, MoTa: 'Phân tích cách con người ra quyết định thông qua hai hệ thống tư duy nhanh và chậm.', AnhBia: '/images/books/thinking-fast-and-slow.svg' },
  { MaSach: 'S009', TenSach: 'Lược sử thời gian', NguonGoc: 'Stephen Hawking', avgRating: 4.3, ratingCount: 980, TheLoai: 'Khoa học', NamXuatBan: 2016, SoQuyen: 4, MoTa: 'Cuốn sách khoa học phổ thông kinh điển giải thích vũ trụ, thời gian và các bí ẩn vật lý.', AnhBia: '/images/books/luoc-su-thoi-gian.svg' },
  { MaSach: 'S010', TenSach: 'Đi tìm lẽ sống', NguonGoc: 'Viktor E. Frankl', avgRating: 4.9, ratingCount: 2711, TheLoai: 'Tâm lý', NamXuatBan: 2022, SoQuyen: 9, MoTa: 'Tác phẩm nổi tiếng về ý nghĩa cuộc sống, nghị lực và sức mạnh tinh thần của con người.', AnhBia: '/images/books/di-tim-le-song.svg' }
];

const state = { search: '', genre: 'all', author: 'all', minRating: 0, yearFrom: 0, yearBefore: null, sort: 'newest', page: 1, pageSize: 10 };
const genres = ['all', 'Tiểu thuyết', 'Kinh tế', 'Kỹ năng sống', 'Tâm lý', 'Khoa học', 'Lịch sử'];
const authors = ['all', 'Nguyễn Nhật Ánh', 'Paulo Coelho', 'Dale Carnegie', 'Yuval Noah Harari', 'Nhiều tác giả', 'Nguyên Phong', 'Rosie Nguyễn', 'Daniel Kahneman', 'Stephen Hawking', 'Viktor E. Frankl'];
const ratings = [5, 4, 3, 2, 1];
const years = [
  { label: 'Tất cả', from: 0, before: null },
  { label: '2022+', from: 2022, before: null },
  { label: '2020+', from: 2020, before: null },
  { label: '2018+', from: 2018, before: null },
  { label: 'Trước 2018', from: 0, before: 2018 }
];

const $ = (id) => document.getElementById(id);
const genreFilters = $('genre-filters');
const authorFilters = $('author-filters');
const ratingFilters = $('rating-filters');
const yearFilters = $('year-filters');
const authorSearch = $('author-search');
const topSearch = $('top-search');
const topVoice = $('top-voice');
const sortSelect = $('sort-select');
const resultTitle = $('result-title');
const bookItems = $('book-items');
const pagingInfo = $('paging-info');
const prevPage = $('prev-page');
const nextPage = $('next-page');

const chatFab = $('chat-fab');
const chatPanel = $('chat-fab-panel');
const chatBox = $('chat-box');
const chatInput = $('chat-input');
const chatSend = $('chat-send');

function renderFilter(container, items, current, onPick, formatter = (x) => x, keyFn = (x) => x) {
  container.innerHTML = '';
  items.forEach((item) => {
    const btn = document.createElement('button');
    btn.className = `filter-item ${keyFn(item) === current ? 'active' : ''}`;
    btn.textContent = formatter(item);
    btn.addEventListener('click', () => onPick(item));
    container.appendChild(btn);
  });
}

function renderFilters() {
  renderFilter(genreFilters, genres, state.genre, (item) => { state.genre = item; state.page = 1; renderFilters(); fetchBooks(); }, (x) => (x === 'all' ? 'Tất cả' : x));
  const kw = authorSearch.value.trim().toLowerCase();
  const authorList = authors.filter((a) => a === 'all' || a.toLowerCase().includes(kw));
  renderFilter(authorFilters, authorList, state.author, (item) => { state.author = item; state.page = 1; renderFilters(); fetchBooks(); }, (x) => (x === 'all' ? 'Tất cả' : x));
  renderFilter(ratingFilters, ratings, state.minRating, (item) => { state.minRating = item; state.page = 1; renderFilters(); fetchBooks(); }, (x) => `${'⭐'.repeat(x)}`, (x) => x);
  renderFilter(yearFilters, years, `${state.yearFrom}-${state.yearBefore}`, (item) => { state.yearFrom = item.from; state.yearBefore = item.before; state.page = 1; renderFilters(); fetchBooks(); }, (x) => x.label, (x) => `${x.from}-${x.before}`);
}

const formatCount = (n) => Number(n || 0).toLocaleString('vi-VN');

function sortBooks(list) {
  const arr = [...list];
  const sorts = {
    newest: (a, b) => (b.NamXuatBan || 0) - (a.NamXuatBan || 0),
    rating: (a, b) => (b.avgRating || 0) - (a.avgRating || 0),
    titleAZ: (a, b) => (a.TenSach || '').localeCompare(b.TenSach || ''),
    quantityDesc: (a, b) => (b.SoQuyen || 0) - (a.SoQuyen || 0),
    priceAsc: (a, b) => (a.DonGia || 0) - (b.DonGia || 0),
    priceDesc: (a, b) => (b.DonGia || 0) - (a.DonGia || 0)
  };
  arr.sort(sorts[state.sort] || sorts.newest);
  return arr;
}

function applyClientFilters(list) {
  const q = state.search.toLowerCase();
  return sortBooks(
    list.filter((b) => {
      if (q && !`${b.TenSach} ${b.NguonGoc} ${b.TheLoai}`.toLowerCase().includes(q)) return false;
      if (state.genre !== 'all' && b.TheLoai !== state.genre) return false;
      if (state.author !== 'all' && b.NguonGoc !== state.author) return false;
      if (state.minRating > 0 && (b.avgRating || 0) < state.minRating) return false;
      if (state.yearFrom > 0 && (b.NamXuatBan || 0) < state.yearFrom) return false;
      if (state.yearBefore && (b.NamXuatBan || 0) >= state.yearBefore) return false;
      return true;
    })
  );
}

function bookRow(book) {
  return `
    <a href="/book-detail.html?id=${encodeURIComponent(book.MaSach)}" class="book-row card border-0 shadow-sm text-decoration-none text-dark">
      <div class="card-body d-flex gap-3">
        <img src="${book.AnhBia || '/images/books/placeholder-book.svg'}" class="book-row-cover" alt="${book.TenSach}" />
        <div class="flex-grow-1">
          <h5 class="mb-1">${book.TenSach}</h5>
          <div class="text-muted small mb-1">${book.NguonGoc || 'Nhiều tác giả'}</div>
          <div class="small mb-1">★ ${Number(book.avgRating || 0).toFixed(1)} (${formatCount(book.ratingCount)} đánh giá)</div>
          <div class="small text-muted mb-1">${book.TheLoai || 'Chưa rõ thể loại'}</div>
          <div class="small text-secondary line-clamp-2">${book.MoTa || ''}</div>
        </div>
      </div>
    </a>
  `;
}

function renderBooks(allBooks) {
  const filtered = applyClientFilters(allBooks);
  const start = (state.page - 1) * state.pageSize;
  const pageBooks = filtered.slice(start, start + state.pageSize);
  resultTitle.textContent = `Tất cả sách (${formatCount(filtered.length)})`;
  bookItems.innerHTML = pageBooks.length ? pageBooks.map(bookRow).join('') : '<div class="alert alert-light border">Không có sách phù hợp bộ lọc.</div>';
  const totalPages = Math.max(1, Math.ceil(filtered.length / state.pageSize));
  pagingInfo.textContent = `Trang ${state.page}/${totalPages} • ${formatCount(filtered.length)} kết quả`;
  prevPage.disabled = state.page <= 1;
  nextPage.disabled = state.page >= totalPages;
}

async function fetchBooks() {
  try {
    const q = new URLSearchParams({ search: state.search, genre: state.genre, author: state.author, minRating: String(state.minRating), yearFrom: String(state.yearFrom), sort: state.sort, page: String(state.page), pageSize: String(state.pageSize) });
    const res = await fetch(`/api/books?${q.toString()}`);
    const data = await res.json();
    const apiBooks = (data.books || []).map((b) => ({ ...b, avgRating: booksById[b.MaSach]?.avgRating ?? b.avgRating, ratingCount: booksById[b.MaSach]?.ratingCount ?? b.ratingCount, MoTa: booksById[b.MaSach]?.MoTa ?? b.MoTa, AnhBia: booksById[b.MaSach]?.AnhBia ?? b.AnhBia }));
    if (!apiBooks.length) {
      renderBooks(MOCK_BOOKS);
      return;
    }
    renderBooks(apiBooks);
  } catch (error) {
    renderBooks(MOCK_BOOKS);
  }
}

const booksById = Object.fromEntries(MOCK_BOOKS.map((b) => [b.MaSach, b]));

sortSelect.addEventListener('change', () => { state.sort = sortSelect.value; state.page = 1; fetchBooks(); });
authorSearch.addEventListener('input', renderFilters);
topSearch.addEventListener('keydown', (e) => { if (e.key === 'Enter') { state.search = topSearch.value.trim(); state.page = 1; fetchBooks(); } });

topVoice.addEventListener('click', () => {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) { alert('Trình duyệt chưa hỗ trợ Web Speech API.'); return; }
  const recognition = new SpeechRecognition();
  recognition.lang = 'vi-VN';
  recognition.start();
  recognition.onresult = (event) => {
    const text = event.results[0][0].transcript;
    topSearch.value = text;
    state.search = text;
    state.page = 1;
    fetchBooks();
  };
});

prevPage.addEventListener('click', () => { if (state.page > 1) { state.page -= 1; fetchBooks(); } });
nextPage.addEventListener('click', () => { state.page += 1; fetchBooks(); });

function addChatBubble(text, who = 'bot') {
  const bubble = document.createElement('div');
  bubble.className = `chat-bubble ${who === 'user' ? 'chat-user' : 'chat-bot'}`;
  bubble.textContent = text;
  chatBox.appendChild(bubble);
  chatBox.scrollTop = chatBox.scrollHeight;
}

chatFab.addEventListener('click', () => chatPanel.classList.toggle('d-none'));
chatSend.addEventListener('click', async () => {
  const message = chatInput.value.trim();
  if (!message) return;
  addChatBubble(message, 'user');
  chatInput.value = '';
  const res = await fetch('/api/chatbot', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ message }) });
  const data = await res.json();
  addChatBubble(data.reply, 'bot');
});

addChatBubble('Xin chào, mình hỗ trợ tìm sách theo nhu cầu của bạn!');
renderFilters();
fetchBooks();

const booksMockMeta = {
  S001: { rating: 4.6, reviewCount: 2345, description: 'Cuốn sách nổi tiếng về nghệ thuật giao tiếp, ứng xử và xây dựng mối quan hệ hiệu quả.' },
  S002: { rating: 4.7, reviewCount: 1780, description: 'Hành trình theo đuổi ước mơ và lắng nghe trái tim qua câu chuyện đầy tính biểu tượng.' },
  S003: { rating: 4.8, reviewCount: 3507, description: 'Tác phẩm khái quát lịch sử phát triển của loài người từ thời nguyên thủy đến hiện đại.' },
  S004: { rating: 4.5, reviewCount: 1234, description: 'Tuyển tập các câu chuyện truyền cảm hứng về nghị lực, niềm tin và lòng biết ơn.' },
  S005: { rating: 4.6, reviewCount: 1887, description: 'Phương pháp học tập, phát triển tư duy và tạo động lực cho học sinh, sinh viên.' },
  S006: { rating: 4.4, reviewCount: 1642, description: 'Tác phẩm kết hợp yếu tố chiêm nghiệm, tâm linh và góc nhìn về nhân quả trong cuộc sống.' },
  S007: { rating: 4.5, reviewCount: 2104, description: 'Những chia sẻ gần gũi về học tập, trải nghiệm, trưởng thành và giá trị của tuổi trẻ.' },
  S008: { rating: 4.7, reviewCount: 1420, description: 'Phân tích cách con người ra quyết định thông qua hai hệ thống tư duy nhanh và chậm.' },
  S009: { rating: 4.3, reviewCount: 980, description: 'Cuốn sách khoa học phổ thông kinh điển giải thích vũ trụ, thời gian và các bí ẩn vật lý.' },
  S010: { rating: 4.9, reviewCount: 2711, description: 'Tác phẩm nổi tiếng về ý nghĩa cuộc sống, nghị lực và sức mạnh tinh thần của con người.' }
};

const state = {
  search: '',
  genre: 'all',
  author: 'all',
  minRating: 0,
  yearFrom: 0,
  yearBefore: null,
  sort: 'newest',
  page: 1,
  pageSize: 10
};

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

const genreFilters = document.getElementById('genre-filters');
const authorFilters = document.getElementById('author-filters');
const ratingFilters = document.getElementById('rating-filters');
const yearFilters = document.getElementById('year-filters');
const authorSearch = document.getElementById('author-search');
const topSearch = document.getElementById('top-search');
const topVoice = document.getElementById('top-voice');
const sortSelect = document.getElementById('sort-select');
const resultTitle = document.getElementById('result-title');
const bookItems = document.getElementById('book-items');
const pagingInfo = document.getElementById('paging-info');
const prevPage = document.getElementById('prev-page');
const nextPage = document.getElementById('next-page');

const chatFab = document.getElementById('chat-fab');
const chatPanel = document.getElementById('chat-fab-panel');
const chatBox = document.getElementById('chat-box');
const chatInput = document.getElementById('chat-input');
const chatSend = document.getElementById('chat-send');

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
  renderFilter(genreFilters, genres, state.genre, (item) => {
    state.genre = item;
    state.page = 1;
    renderFilters();
    fetchBooks();
  }, (x) => (x === 'all' ? 'Tất cả' : x));

  const kw = authorSearch.value.trim().toLowerCase();
  const authorList = authors.filter((a) => a === 'all' || a.toLowerCase().includes(kw));
  renderFilter(authorFilters, authorList, state.author, (item) => {
    state.author = item;
    state.page = 1;
    renderFilters();
    fetchBooks();
  }, (x) => (x === 'all' ? 'Tất cả' : x));

  renderFilter(ratingFilters, ratings, state.minRating, (item) => {
    state.minRating = item;
    state.page = 1;
    renderFilters();
    fetchBooks();
  }, (x) => `${'⭐'.repeat(x)}`, (x) => x);

  renderFilter(yearFilters, years, `${state.yearFrom}-${state.yearBefore}`, (item) => {
    state.yearFrom = item.from;
    state.yearBefore = item.before;
    state.page = 1;
    renderFilters();
    fetchBooks();
  }, (x) => x.label, (x) => `${x.from}-${x.before}`);
}

function formatCount(n) {
  return Number(n || 0).toLocaleString('vi-VN');
}

function bookRow(book) {
  const meta = booksMockMeta[book.MaSach] || {};
  const rating = meta.rating ?? Number(book.avgRating || 0);
  const reviewCount = meta.reviewCount ?? Number(book.ratingCount || 0);
  const description = meta.description || book.MoTa || '';
  const cover = book.AnhBia || '/images/books/placeholder-book.svg';

  return `
    <a href="/book-detail.html?id=${encodeURIComponent(book.MaSach)}" class="book-row card border-0 shadow-sm text-decoration-none text-dark">
      <div class="card-body d-flex gap-3">
        <img src="${cover}" class="book-row-cover" alt="${book.TenSach}" />
        <div class="flex-grow-1">
          <h5 class="mb-1">${book.TenSach}</h5>
          <div class="text-muted small mb-1">${book.NguonGoc || 'Nhiều tác giả'}</div>
          <div class="small mb-1">★ ${Number(rating).toFixed(1)} (${formatCount(reviewCount)} đánh giá)</div>
          <div class="small text-muted mb-1">${book.TheLoai || 'Chưa rõ thể loại'}</div>
          <div class="small text-secondary line-clamp-2">${description}</div>
        </div>
      </div>
    </a>
  `;
}

async function fetchBooks() {
  const q = new URLSearchParams({
    search: state.search,
    genre: state.genre,
    author: state.author,
    minRating: String(state.minRating),
    yearFrom: String(state.yearFrom),
    sort: state.sort,
    page: String(state.page),
    pageSize: String(state.pageSize)
  });

  const res = await fetch(`/api/books?${q.toString()}`);
  const data = await res.json();

  let books = data.books || [];
  if (state.yearBefore) {
    books = books.filter((b) => Number(b.NamXuatBan || 0) < state.yearBefore);
  }

  const total = state.yearBefore ? books.length : data.pagination?.total || books.length;
  resultTitle.textContent = `Tất cả sách (${formatCount(total)})`;

  bookItems.innerHTML = books.map(bookRow).join('');
  if (!books.length) {
    bookItems.innerHTML = '<div class="alert alert-light border">Không có sách phù hợp bộ lọc.</div>';
  }

  pagingInfo.textContent = `Trang ${data.pagination?.page || 1}/${data.pagination?.totalPages || 1} • ${formatCount(total)} kết quả`;

  prevPage.disabled = (data.pagination?.page || 1) <= 1;
  nextPage.disabled = (data.pagination?.page || 1) >= (data.pagination?.totalPages || 1);
}

sortSelect.addEventListener('change', () => {
  state.sort = sortSelect.value;
  state.page = 1;
  fetchBooks();
});

authorSearch.addEventListener('input', renderFilters);

topSearch.addEventListener('keydown', (e) => {
  if (e.key === 'Enter') {
    state.search = topSearch.value.trim();
    state.page = 1;
    fetchBooks();
  }
});

topVoice.addEventListener('click', () => {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    alert('Trình duyệt chưa hỗ trợ Web Speech API.');
    return;
  }

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

prevPage.addEventListener('click', () => {
  if (state.page > 1) {
    state.page -= 1;
    fetchBooks();
  }
});

nextPage.addEventListener('click', () => {
  state.page += 1;
  fetchBooks();
});

function addChatBubble(text, who = 'bot') {
  const bubble = document.createElement('div');
  bubble.className = `chat-bubble ${who === 'user' ? 'chat-user' : 'chat-bot'}`;
  bubble.textContent = text;
  chatBox.appendChild(bubble);
  chatBox.scrollTop = chatBox.scrollHeight;
}

chatFab.addEventListener('click', () => {
  chatPanel.classList.toggle('d-none');
});

chatSend.addEventListener('click', async () => {
  const message = chatInput.value.trim();
  if (!message) return;
  addChatBubble(message, 'user');
  chatInput.value = '';

  const res = await fetch('/api/chatbot', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ message })
  });
  const data = await res.json();
  addChatBubble(data.reply, 'bot');
});

addChatBubble('Xin chào, mình hỗ trợ tìm sách theo nhu cầu của bạn!');
renderFilters();
fetchBooks();

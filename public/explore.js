const state = {
  search: '',
  genre: 'all',
  author: 'all',
  minRating: 0,
  yearFrom: 0,
  sort: 'newest',
  page: 1,
  pageSize: 10
};

const genres = ['all', 'Tiểu thuyết', 'Kinh tế', 'Kỹ năng sống', 'Tâm lý', 'Khoa học', 'Lịch sử'];
const authors = ['all', 'Nguyễn Nhật Ánh', 'Paulo Coelho', 'Dale Carnegie', 'Yuval Noah Harari', 'Nhiều tác giả'];
const ratings = [5, 4, 3, 2, 1];
const years = [0, 2020, 2015, 2010];

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

function renderFilter(container, items, current, onPick, formatter = (x) => x) {
  container.innerHTML = '';
  items.forEach((item) => {
    const btn = document.createElement('button');
    btn.className = `filter-item ${item === current ? 'active' : ''}`;
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
  }, (x) => `${'⭐'.repeat(x)} trở lên`);

  renderFilter(yearFilters, years, state.yearFrom, (item) => {
    state.yearFrom = item;
    state.page = 1;
    renderFilters();
    fetchBooks();
  }, (x) => (x === 0 ? 'Tất cả' : `${x}+`));
}

function formatCount(n) {
  return Number(n || 0).toLocaleString('vi-VN');
}

function bookRow(book) {
  return `
    <a href="/book-detail.html?id=${encodeURIComponent(book.MaSach)}" class="book-row card border-0 shadow-sm text-decoration-none text-dark">
      <div class="card-body d-flex gap-3">
        <img src="${book.AnhBia || 'https://placehold.co/120x170'}" class="book-row-cover" alt="${book.TenSach}" />
        <div class="flex-grow-1">
          <h5 class="mb-1">${book.TenSach}</h5>
          <div class="text-muted small mb-1">${book.NguonGoc || 'Nhiều tác giả'}</div>
          <div class="small mb-1">⭐ ${Number(book.avgRating || 0).toFixed(1)} (${formatCount(book.ratingCount)} đánh giá)</div>
          <div class="small text-muted">${book.TheLoai || 'Chưa rõ thể loại'}</div>
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

  const total = data.pagination?.total || 0;
  resultTitle.textContent = `Tất cả sách (${formatCount(total)})`;

  bookItems.innerHTML = (data.books || []).map(bookRow).join('');
  if (!data.books?.length) {
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

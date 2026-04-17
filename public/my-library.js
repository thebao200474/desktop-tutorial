const myBooks = [
  { id: 'S001', title: 'Đắc nhân tâm', author: 'Dale Carnegie', status: 'read', rating: 5, cover: '/images/books/dac-nhan-tam.svg' },
  { id: 'S002', title: 'Nhà giả kim', author: 'Paulo Coelho', status: 'reading', rating: 4, cover: '/images/books/nha-gia-kim.svg' },
  { id: 'S003', title: 'Sapiens', author: 'Yuval Noah Harari', status: 'wishlist', rating: 5, cover: '/images/books/sapiens.svg' },
  { id: 'S004', title: 'Hạt giống tâm hồn', author: 'Nhiều tác giả', status: 'read', rating: 4, cover: '/images/books/hat-giong-tam-hon.svg' },
  { id: 'S005', title: 'Tôi tài giỏi, bạn cũng thế!', author: 'Adam Khoo', status: 'reading', rating: 4, cover: '/images/books/toi-tai-gioi-ban-cung-the.svg' },
  { id: 'S006', title: 'Chó sủa án trăng', author: 'Nguyên Hương', status: 'wishlist', rating: 4, cover: '/images/books/placeholder-book.svg' },
  { id: 'S007', title: 'Muôn kiếp nhân sinh', author: 'Nguyên Phong', status: 'read', rating: 5, cover: '/images/books/muon-kiep-nhan-sinh.svg' },
  { id: 'S008', title: 'Lược sử thời gian', author: 'Stephen Hawking', status: 'wishlist', rating: 4, cover: '/images/books/luoc-su-thoi-gian.svg' }
];

const statuses = [
  { key: 'all', label: 'Tất cả' },
  { key: 'read', label: 'Đã đọc' },
  { key: 'reading', label: 'Đang đọc' },
  { key: 'wishlist', label: 'Muốn đọc' }
];

let currentStatus = 'all';

const statusTabs = document.getElementById('status-tabs');
const libraryGrid = document.getElementById('library-grid');
const libTitle = document.getElementById('lib-title');

function countByStatus(key) {
  return key === 'all' ? myBooks.length : myBooks.filter((b) => b.status === key).length;
}

function renderTabs() {
  statusTabs.innerHTML = '';
  statuses.forEach((s) => {
    const btn = document.createElement('button');
    btn.className = `btn btn-sm ${currentStatus === s.key ? 'btn-brown' : 'btn-outline-secondary'} rounded-pill`;
    btn.textContent = `${s.label} (${countByStatus(s.key)})`;
    btn.addEventListener('click', () => {
      currentStatus = s.key;
      renderTabs();
      renderBooks();
    });
    statusTabs.appendChild(btn);
  });
}

function renderBooks() {
  const data = currentStatus === 'all' ? myBooks : myBooks.filter((b) => b.status === currentStatus);
  libTitle.textContent = `Giá sách của tôi (${data.length})`;

  libraryGrid.innerHTML = data
    .map(
      (book) => `
      <div class="col-6 col-md-4 col-xl-3">
        <a href="/book-detail.html?id=${encodeURIComponent(book.id)}" class="text-decoration-none text-dark">
          <div class="card border-0 shadow-sm library-book-card h-100">
            <img src="${book.cover}" class="card-img-top library-book-cover" alt="${book.title}">
            <div class="card-body py-2">
              <div class="fw-semibold small line-clamp-2">${book.title}</div>
              <div class="text-muted small">${book.author}</div>
              <div class="small text-warning">${'★'.repeat(book.rating)}</div>
            </div>
          </div>
        </a>
      </div>
    `
    )
    .join('');
}

// chatbot
const chatFab = document.getElementById('chat-fab');
const chatPanel = document.getElementById('chat-fab-panel');
const chatBox = document.getElementById('chat-box');
const chatInput = document.getElementById('chat-input');
const chatSend = document.getElementById('chat-send');

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

  const res = await fetch('/api/chatbot', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ message })
  });

  const data = await res.json();
  addChatBubble(data.reply, 'bot');
});

addChatBubble('Xin chào! Đây là giá sách cá nhân của bạn.');
renderTabs();
renderBooks();

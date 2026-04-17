const state = {
  token: localStorage.getItem('readerToken') || '',
  profile: JSON.parse(localStorage.getItem('readerProfile') || 'null')
};

const bookList = document.getElementById('book-list');
const searchInput = document.getElementById('search-input');
const searchBtn = document.getElementById('search-btn');
const voiceBtn = document.getElementById('voice-btn');
const voiceStatus = document.getElementById('voice-status');
const sendOtpReader = document.getElementById('send-otp-reader');
const verifyOtpReader = document.getElementById('verify-otp-reader');
const readerEmailInput = document.getElementById('reader-email');
const readerOtpInput = document.getElementById('reader-otp');
const authStatus = document.getElementById('auth-status');
const chatBox = document.getElementById('chat-box');
const chatInput = document.getElementById('chat-input');
const chatSend = document.getElementById('chat-send');
const loanList = document.getElementById('loan-list');

readerEmailInput.value = 'docgia1@example.com';

function addChatBubble(text, who = 'bot') {
  const bubble = document.createElement('div');
  bubble.className = `chat-bubble ${who === 'user' ? 'chat-user' : 'chat-bot'}`;
  bubble.textContent = text;
  chatBox.appendChild(bubble);
  chatBox.scrollTop = chatBox.scrollHeight;
}

async function fetchBooks(q = '') {
  const res = await fetch(`/api/search-books?q=${encodeURIComponent(q)}`);
  const data = await res.json();

  bookList.innerHTML = '';
  data.books.forEach((book) => {
    const col = document.createElement('div');
    col.className = 'col-md-6';
    col.innerHTML = `
      <div class="card h-100 shadow-sm book-card">
        <img src="${book.AnhBia || 'https://placehold.co/300x220'}" class="card-img-top" alt="${book.TenSach}">
        <div class="card-body">
          <h5 class="card-title">${book.TenSach}</h5>
          <p class="card-text mb-1"><strong>Tác giả/Nguồn gốc:</strong> ${book.NguonGoc || 'Đang cập nhật'}</p>
          <p class="card-text mb-1"><strong>Thể loại:</strong> ${book.TheLoai || 'Đang cập nhật'}</p>
          <p class="card-text mb-1"><strong>Số quyển:</strong> ${book.SoQuyen}</p>
          <p class="card-text">${book.MoTa || ''}</p>
          <button class="btn btn-sm btn-primary borrow-btn" data-id="${book.MaSach}">Mượn sách</button>
        </div>
      </div>
    `;
    bookList.appendChild(col);
  });

  document.querySelectorAll('.borrow-btn').forEach((btn) => {
    btn.addEventListener('click', () => borrowBook(btn.dataset.id));
  });
}

async function borrowBook(MaSach) {
  if (!state.token) {
    alert('Vui lòng xác thực OTP trước khi mượn sách.');
    return;
  }

  const res = await fetch('/api/borrow-book', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${state.token}`
    },
    body: JSON.stringify({ MaSach })
  });

  const data = await res.json();
  alert(data.message);
  await fetchBooks(searchInput.value);
  await loadMyLoans();
}

searchBtn.addEventListener('click', () => fetchBooks(searchInput.value));

voiceBtn.addEventListener('click', () => {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    voiceStatus.textContent = 'Trình duyệt chưa hỗ trợ Web Speech API.';
    return;
  }

  const recognition = new SpeechRecognition();
  recognition.lang = 'vi-VN';
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;

  voiceStatus.textContent = 'Đang nghe...';
  recognition.start();

  recognition.onresult = (event) => {
    const text = event.results[0][0].transcript;
    searchInput.value = text;
    voiceStatus.textContent = `Đã nhận: "${text}"`;
    fetchBooks(text);
  };

  recognition.onerror = () => {
    voiceStatus.textContent = 'Không nhận diện được giọng nói, vui lòng thử lại.';
  };
});

sendOtpReader.addEventListener('click', async () => {
  const email = readerEmailInput.value.trim();
  const res = await fetch('/api/auth/send-otp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, role: 'reader' })
  });
  const data = await res.json();
  authStatus.textContent = data.message || data.error;
});

verifyOtpReader.addEventListener('click', async () => {
  const email = readerEmailInput.value.trim();
  const otp = readerOtpInput.value.trim();

  const res = await fetch('/api/auth/verify-otp', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, otp, role: 'reader' })
  });

  const data = await res.json();
  if (data.token) {
    state.token = data.token;
    state.profile = data.profile;
    localStorage.setItem('readerToken', data.token);
    localStorage.setItem('readerProfile', JSON.stringify(data.profile));
    authStatus.textContent = `Xin chào ${data.profile.name}, xác thực thành công.`;
    loadMyLoans();
  } else {
    authStatus.textContent = data.message || 'Xác thực thất bại.';
  }
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

async function loadMyLoans() {
  if (!state.token) {
    loanList.innerHTML = '<li class="list-group-item">Chưa đăng nhập OTP.</li>';
    return;
  }

  const res = await fetch('/api/my-loans', {
    headers: { Authorization: `Bearer ${state.token}` }
  });
  const data = await res.json();

  loanList.innerHTML = '';
  if (!data.loans?.length) {
    loanList.innerHTML = '<li class="list-group-item">Chưa có lượt mượn nào.</li>';
    return;
  }

  data.loans.forEach((loan) => {
    const item = document.createElement('li');
    item.className = 'list-group-item';
    item.textContent = `${loan.TenSach} - ${loan.TrangThai} (Hạn trả: ${new Date(loan.HanTra).toLocaleDateString('vi-VN')})`;
    loanList.appendChild(item);
  });
}

addChatBubble('Xin chào! Mình có thể gợi ý sách hoặc hướng dẫn mượn sách cho bạn.');
fetchBooks();
loadMyLoans();

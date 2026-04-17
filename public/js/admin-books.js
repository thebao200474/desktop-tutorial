const state = {
  token: localStorage.getItem('adminToken') || '',
  books: [],
  query: '',
  currentPage: 1,
  pageSize: 5,
  editingId: null
};

if (!state.token) {
  window.location.href = '/admin-login.html';
}

const booksTable = document.getElementById('books-table');
const paginationEl = document.getElementById('books-pagination');
const searchInput = document.getElementById('book-search');
const voiceBtn = document.getElementById('book-voice-btn');
const voiceStatus = document.getElementById('voice-status');
const bookForm = document.getElementById('book-form');
const bookModalEl = document.getElementById('book-modal');
const bookModal = new bootstrap.Modal(bookModalEl);
const modalTitle = document.getElementById('book-modal-title');
const submitBtn = document.getElementById('book-submit-btn');

function getField(id) {
  return document.getElementById(id);
}

async function api(path, options = {}) {
  const response = await fetch(path, {
    ...options,
    headers: {
      ...(options.headers || {}),
      Authorization: `Bearer ${state.token}`,
      'Content-Type': 'application/json'
    }
  });

  if (response.status === 401) {
    localStorage.removeItem('adminToken');
    window.location.href = '/admin-login.html';
    return {};
  }

  return response.json();
}

function logout() {
  localStorage.removeItem('adminToken');
  window.location.href = '/admin-login.html';
}

document.getElementById('admin-logout').addEventListener('click', logout);

function normalizeText(value) {
  return String(value || '')
    .toLowerCase()
    .normalize('NFD')
    .replace(/\p{Diacritic}/gu, '');
}

function getFilteredBooks() {
  const q = normalizeText(state.query.trim());
  if (!q) return state.books;

  return state.books.filter((book) => {
    const fields = [
      book.MaSach,
      book.TenSach,
      book.NguonGoc,
      book.TheLoai,
      book.MoTa,
      book.MaNXB,
      book.NamXuatBan,
      book.SoQuyen,
      book.DonGia
    ];

    return fields.some((field) => normalizeText(field).includes(q));
  });
}

function getPageItems(items) {
  const start = (state.currentPage - 1) * state.pageSize;
  return items.slice(start, start + state.pageSize);
}

function renderPagination(totalItems) {
  const totalPages = Math.max(1, Math.ceil(totalItems / state.pageSize));
  if (state.currentPage > totalPages) state.currentPage = totalPages;

  paginationEl.innerHTML = '';

  const createPageItem = (label, page, disabled = false, active = false) => {
    const li = document.createElement('li');
    li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;

    const btn = document.createElement('button');
    btn.className = 'page-link';
    btn.type = 'button';
    btn.textContent = label;
    btn.disabled = disabled;
    btn.addEventListener('click', () => {
      state.currentPage = page;
      renderTable();
    });

    li.appendChild(btn);
    paginationEl.appendChild(li);
  };

  createPageItem('‹', Math.max(1, state.currentPage - 1), state.currentPage === 1);
  for (let page = 1; page <= totalPages; page += 1) {
    createPageItem(String(page), page, false, page === state.currentPage);
  }
  createPageItem('›', Math.min(totalPages, state.currentPage + 1), state.currentPage === totalPages);
}

function renderTable() {
  const filtered = getFilteredBooks();
  const pageItems = getPageItems(filtered);

  booksTable.innerHTML = '';

  if (!pageItems.length) {
    booksTable.innerHTML =
      '<tr><td colspan="7" class="text-center text-muted py-4">Không tìm thấy sách phù hợp.</td></tr>';
    renderPagination(0);
    return;
  }

  pageItems.forEach((book) => {
    const tr = document.createElement('tr');
    const cover = book.AnhBia || '/images/books/placeholder-book.svg';

    tr.innerHTML = `
      <td>
        <img src="${cover}" class="admin-book-thumb" alt="${book.TenSach}" onerror="this.src='/images/books/placeholder-book.svg'" />
      </td>
      <td>${book.MaSach}</td>
      <td>
        <div class="fw-semibold">${book.TenSach}</div>
        <small class="text-muted">${book.MoTa ? book.MoTa.slice(0, 70) : 'Chưa có mô tả'}</small>
      </td>
      <td>${book.NguonGoc || '-'}</td>
      <td>${book.TheLoai || '-'}</td>
      <td><span class="badge text-bg-light">${book.SoQuyen ?? 0}</span></td>
      <td>
        <button class="btn btn-sm btn-outline-warning me-1 edit"><i class="bi bi-pencil"></i></button>
        <button class="btn btn-sm btn-outline-danger del"><i class="bi bi-trash"></i></button>
      </td>
    `;

    tr.querySelector('.edit').addEventListener('click', () => openEditModal(book));
    tr.querySelector('.del').addEventListener('click', async () => {
      if (!window.confirm(`Xóa sách ${book.TenSach}?`)) return;
      await api(`/api/admin/books/${book.MaSach}`, { method: 'DELETE' });
      await loadBooks();
    });

    booksTable.appendChild(tr);
  });

  renderPagination(filtered.length);
}

function fillForm(book = null) {
  getField('book-id').value = book?.MaSach || '';
  getField('book-id').disabled = Boolean(book);
  getField('book-name').value = book?.TenSach || '';
  getField('book-author').value = book?.NguonGoc || '';
  getField('book-category').value = book?.TheLoai || '';
  getField('book-qty').value = book?.SoQuyen ?? 0;
  getField('book-price').value = book?.DonGia ?? 0;
  getField('book-year').value = book?.NamXuatBan ?? 2024;
  getField('book-publisher').value = book?.MaNXB || 'NXB01';
  getField('book-cover').value = book?.AnhBia || '/images/books/placeholder-book.svg';
  getField('book-description').value = book?.MoTa || '';
}

function openAddModal() {
  state.editingId = null;
  modalTitle.textContent = 'Thêm sách';
  submitBtn.textContent = 'Lưu sách';
  fillForm();
}

function openEditModal(book) {
  state.editingId = book.MaSach;
  modalTitle.textContent = `Sửa sách ${book.MaSach}`;
  submitBtn.textContent = 'Cập nhật';
  fillForm(book);
  bookModal.show();
}

bookModalEl.addEventListener('show.bs.modal', () => {
  if (!state.editingId) openAddModal();
});

bookForm.addEventListener('submit', async (event) => {
  event.preventDefault();

  const payload = {
    MaSach: getField('book-id').value.trim(),
    TenSach: getField('book-name').value.trim(),
    NguonGoc: getField('book-author').value.trim(),
    TheLoai: getField('book-category').value.trim(),
    SoQuyen: Number(getField('book-qty').value || 0),
    DonGia: Number(getField('book-price').value || 0),
    NamXuatBan: Number(getField('book-year').value || 2024),
    MaNXB: getField('book-publisher').value.trim() || 'NXB01',
    AnhBia: getField('book-cover').value.trim() || '/images/books/placeholder-book.svg',
    MoTa: getField('book-description').value.trim()
  };

  if (!payload.MaSach || !payload.TenSach) {
    alert('Vui lòng nhập mã sách và tên sách.');
    return;
  }

  if (state.editingId) {
    await api(`/api/admin/books/${state.editingId}`, {
      method: 'PUT',
      body: JSON.stringify(payload)
    });
  } else {
    await api('/api/admin/books', {
      method: 'POST',
      body: JSON.stringify(payload)
    });
  }

  bookModal.hide();
  await loadBooks();
});

async function loadBooks() {
  const data = await api('/api/admin/books');
  state.books = data.books || [];
  renderTable();
}

searchInput.addEventListener('input', () => {
  state.query = searchInput.value;
  state.currentPage = 1;
  renderTable();
});

voiceBtn.addEventListener('click', () => {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    alert('Trình duyệt không hỗ trợ tìm kiếm giọng nói.');
    return;
  }

  const recognition = new SpeechRecognition();
  recognition.lang = 'vi-VN';
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;

  voiceStatus.textContent = 'Đang nghe...';
  recognition.start();

  recognition.onresult = (event) => {
    const transcript = event.results?.[0]?.[0]?.transcript || '';
    searchInput.value = transcript;
    state.query = transcript;
    state.currentPage = 1;
    renderTable();
    voiceStatus.textContent = `Đã nhận: "${transcript}"`;
  };

  recognition.onerror = () => {
    voiceStatus.textContent = 'Không nhận diện được giọng nói. Vui lòng thử lại.';
  };

  recognition.onend = () => {
    if (!voiceStatus.textContent.includes('Đã nhận')) {
      voiceStatus.textContent = '';
    }
  };
});

loadBooks();

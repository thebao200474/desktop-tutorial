const params = new URLSearchParams(window.location.search);
const bookId = params.get('id');
const wrap = document.getElementById('book-detail-wrap');
const reviewList = document.getElementById('review-list');
const reviewForm = document.getElementById('review-form');

async function loadBook() {
  if (!bookId) {
    wrap.innerHTML = '<p>Thiếu mã sách.</p>';
    return;
  }

  const res = await fetch(`/api/books/${encodeURIComponent(bookId)}`);
  const data = await res.json();
  if (!data.book) {
    wrap.innerHTML = '<p>Không tìm thấy sách.</p>';
    return;
  }

  const b = data.book;
  wrap.innerHTML = `
    <div class="col-lg-4">
      <img src="${b.AnhBia || 'https://placehold.co/640x900'}" class="img-fluid rounded-3" alt="${b.TenSach}">
    </div>
    <div class="col-lg-8">
      <h2 class="mb-2">${b.TenSach}</h2>
      <p><strong>Tác giả/Nguồn gốc:</strong> ${b.NguonGoc || 'Đang cập nhật'}</p>
      <p><strong>Năm xuất bản:</strong> ${b.NamXuatBan || 'Đang cập nhật'}</p>
      <p><strong>Nhà xuất bản:</strong> ${b.TenNXB || 'Đang cập nhật'}</p>
      <p><strong>Mô tả:</strong> ${b.MoTa || 'Chưa có mô tả chi tiết'}</p>
      <p><strong>Đánh giá trung bình:</strong> ${Number(b.avgRating || 0).toFixed(1)} ⭐ (${b.totalReviews || 0} đánh giá)</p>
      <button class="btn btn-success" id="borrow-now">Mượn sách</button>
      <small class="text-muted d-block mt-2">Đăng nhập OTP ở trang chủ để mượn sách.</small>
    </div>
  `;

  document.getElementById('borrow-now').addEventListener('click', borrowFromDetail);
}

async function loadReviews() {
  const res = await fetch(`/api/books/${encodeURIComponent(bookId)}/reviews`);
  const data = await res.json();
  reviewList.innerHTML = '';

  if (!data.reviews?.length) {
    reviewList.innerHTML = '<li class="list-group-item">Chưa có đánh giá nào.</li>';
    return;
  }

  data.reviews.forEach((r) => {
    const li = document.createElement('li');
    li.className = 'list-group-item';
    li.innerHTML = `<strong>${r.DocGia || 'Ẩn danh'}</strong> - ${'⭐'.repeat(r.Diem)}<div class="small text-muted">${r.BinhLuan || ''}</div>`;
    reviewList.appendChild(li);
  });
}

async function borrowFromDetail() {
  const token = localStorage.getItem('readerToken');
  if (!token) {
    alert('Vui lòng xác thực OTP ở trang chủ trước khi mượn sách.');
    return;
  }

  const res = await fetch('/api/borrow-book', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`
    },
    body: JSON.stringify({ MaSach: bookId })
  });

  const data = await res.json();
  alert(data.message || 'Đã gửi yêu cầu mượn sách.');
  loadBook();
}

reviewForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const token = localStorage.getItem('readerToken');
  if (!token) {
    alert('Vui lòng đăng nhập OTP để đánh giá sách.');
    return;
  }

  const payload = {
    Diem: Number(document.getElementById('rating').value),
    BinhLuan: document.getElementById('comment').value.trim()
  };

  const res = await fetch(`/api/books/${encodeURIComponent(bookId)}/reviews`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`
    },
    body: JSON.stringify(payload)
  });

  const data = await res.json();
  if (!res.ok) {
    alert(data.message || 'Không thể gửi đánh giá');
    return;
  }

  reviewForm.reset();
  loadBook();
  loadReviews();
});

loadBook();
loadReviews();

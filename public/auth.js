const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');

if (loginForm) {
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('login-email').value.trim();
    const password = document.getElementById('login-password').value;
    const status = document.getElementById('login-status');

    const res = await fetch('/api/auth/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });

    const data = await res.json();
    if (!res.ok) {
      status.textContent = data.message || 'Đăng nhập thất bại.';
      return;
    }

    localStorage.setItem('readerToken', data.token);
    localStorage.setItem('readerProfile', JSON.stringify(data.profile));
    status.textContent = 'Đăng nhập thành công. Đang chuyển về trang chủ...';
    setTimeout(() => {
      window.location.href = '/';
    }, 600);
  });
}

if (registerForm) {
  registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const status = document.getElementById('register-status');

    const Password = document.getElementById('reg-password').value;
    const confirmPassword = document.getElementById('reg-confirm-password').value;
    if (Password !== confirmPassword) {
      status.textContent = 'Mật khẩu xác nhận không khớp.';
      return;
    }

    const payload = {
      HoLot: document.getElementById('reg-holot').value.trim(),
      Ten: document.getElementById('reg-ten').value.trim(),
      Email: document.getElementById('reg-email').value.trim(),
      DienThoai: document.getElementById('reg-phone').value.trim(),
      DiaChi: document.getElementById('reg-address').value.trim(),
      Password
    };

    const res = await fetch('/api/auth/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    status.textContent = data.message || 'Đăng ký xong.';

    if (res.ok) {
      setTimeout(() => {
        window.location.href = '/login.html';
      }, 700);
    }
  });
}

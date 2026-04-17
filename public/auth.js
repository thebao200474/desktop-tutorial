const loginForm = document.getElementById('login-form');
const registerForm = document.getElementById('register-form');

function showRegisterAlert(message, type = 'danger') {
  const box = document.getElementById('register-alert');
  if (!box) return;
  box.className = `alert alert-${type} py-2 small`;
  box.textContent = message;
}

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
  const sendOtpBtn = document.getElementById('send-register-otp');
  let cooldown = 0;
  let timer = null;

  function startCooldown(seconds = 60) {
    cooldown = seconds;
    sendOtpBtn.disabled = true;
    sendOtpBtn.textContent = `Gửi lại sau ${cooldown}s`;
    clearInterval(timer);
    timer = setInterval(() => {
      cooldown -= 1;
      if (cooldown <= 0) {
        clearInterval(timer);
        sendOtpBtn.disabled = false;
        sendOtpBtn.textContent = 'Gửi OTP';
      } else {
        sendOtpBtn.textContent = `Gửi lại sau ${cooldown}s`;
      }
    }, 1000);
  }

  sendOtpBtn.addEventListener('click', async () => {
    const email = document.getElementById('reg-email').value.trim();
    if (!/^\S+@\S+\.\S+$/.test(email)) {
      showRegisterAlert('Email không hợp lệ.');
      return;
    }

    const res = await fetch('/api/auth/send-otp-register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, purpose: 'register' })
    });
    const data = await res.json();
    if (!res.ok || !data.success) {
      showRegisterAlert(data.message || 'Không gửi được OTP.');
      return;
    }

    showRegisterAlert('Đã gửi OTP về Gmail của bạn.', 'success');
    startCooldown(60);
  });

  registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const payload = {
      hoLot: document.getElementById('reg-holot').value.trim(),
      ten: document.getElementById('reg-ten').value.trim(),
      ngaySinh: document.getElementById('reg-dob').value,
      phai: document.getElementById('reg-gender').value,
      diaChi: document.getElementById('reg-address').value.trim(),
      dienThoai: document.getElementById('reg-phone').value.trim(),
      email: document.getElementById('reg-email').value.trim(),
      password: document.getElementById('reg-password').value,
      confirmPassword: document.getElementById('reg-confirm-password').value,
      otp: document.getElementById('reg-otp').value.trim()
    };

    if (!payload.hoLot || !payload.ten || !payload.ngaySinh || !payload.phai || !payload.diaChi || !payload.dienThoai || !payload.email || !payload.password || !payload.confirmPassword || !payload.otp) {
      showRegisterAlert('Vui lòng điền đầy đủ thông tin.');
      return;
    }

    if (!/^\S+@\S+\.\S+$/.test(payload.email)) {
      showRegisterAlert('Email không hợp lệ.');
      return;
    }

    if (!/^\d{9,11}$/.test(payload.dienThoai)) {
      showRegisterAlert('Số điện thoại không hợp lệ.');
      return;
    }

    if (payload.password.length < 6) {
      showRegisterAlert('Mật khẩu tối thiểu 6 ký tự.');
      return;
    }

    if (payload.password !== payload.confirmPassword) {
      showRegisterAlert('Mật khẩu xác nhận không khớp.');
      return;
    }

    if (!/^\d{6}$/.test(payload.otp)) {
      showRegisterAlert('OTP phải gồm 6 chữ số.');
      return;
    }

    const res = await fetch('/api/auth/register', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (!res.ok || !data.success) {
      showRegisterAlert(data.message || 'Đăng ký thất bại.');
      return;
    }

    showRegisterAlert('Đăng ký thành công, chuyển sang trang đăng nhập...', 'success');
    setTimeout(() => {
      window.location.href = '/login.html';
    }, 1500);
  });
}

// chatbot shared in auth pages
const chatFab = document.getElementById('chat-fab');
const chatPanel = document.getElementById('chat-fab-panel');
const chatBox = document.getElementById('chat-box');
const chatInput = document.getElementById('chat-input');
const chatSend = document.getElementById('chat-send');

if (chatFab && chatPanel && chatBox && chatInput && chatSend) {
  const addChatBubble = (text, who = 'bot') => {
    const bubble = document.createElement('div');
    bubble.className = `chat-bubble ${who === 'user' ? 'chat-user' : 'chat-bot'}`;
    bubble.textContent = text;
    chatBox.appendChild(bubble);
    chatBox.scrollTop = chatBox.scrollHeight;
  };

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

  addChatBubble('Xin chào! Mình có thể hỗ trợ bạn đăng ký tài khoản.');
}

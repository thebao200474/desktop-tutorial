    </main>
    <footer class="py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-1">&copy; <?= date('Y'); ?> ChemLearn - CT275 Công nghệ Web</p>
            <p class="mb-0">Học Hóa học dễ hiểu hơn cùng đội ngũ sinh viên Đại học Cần Thơ.</p>
            <button type="button" class="btn btn-outline-light btn-sm d-none mt-3" data-scroll-top>Lên đầu trang</button>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="<?= asset_url('js/main.js'); ?>"></script>

    <!-- ========== CHATBOT BONG BÓNG – CHEMLEARN ========== -->
    <style>
        #chat-bubble-btn {
            position: fixed;
            bottom: 22px;
            right: 22px;
            width: 60px;
            height: 60px;
            background: #0d6efd;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.25);
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 28px;
            z-index: 999999;
        }

        #chatbox-window {
            position: fixed;
            bottom: 95px;
            right: 25px;
            width: 330px;
            max-height: 450px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.25);
            display: none;
            flex-direction: column;
            z-index: 999999;
            overflow: hidden;
        }

        #chatbox-header {
            background: #0d6efd;
            color: #fff;
            padding: 10px 14px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #chatbox-messages {
            padding: 10px;
            height: 320px;
            overflow-y: auto;
            background: #f8f9fa;
            font-size: 0.9rem;
        }

        #chatbox-input-row {
            padding: 8px;
            border-top: 1px solid #dee2e6;
            background: #ffffff;
        }

        .bubble-user {
            background: #0d6efd;
            color: #ffffff;
            padding: 7px 10px;
            border-radius: 15px;
            margin-bottom: 8px;
            max-width: 80%;
            margin-left: auto;
            word-wrap: break-word;
        }

        .bubble-bot {
            background: #e9ecef;
            color: #212529;
            padding: 7px 10px;
            border-radius: 15px;
            margin-bottom: 8px;
            max-width: 80%;
            margin-right: auto;
            word-wrap: break-word;
        }

        #chatbox-input-row input {
            font-size: 0.9rem;
        }

        #chatbox-input-row button {
            font-size: 0.9rem;
        }
    </style>

    <div id="chat-bubble-btn" title="Chatbot Hóa học">
        💬
    </div>

    <div id="chatbox-window">
        <div id="chatbox-header">
            <span>Chatbot Hóa học</span>
            <button type="button" id="chatbox-close-btn" class="btn btn-sm btn-light">
                ×
            </button>
        </div>
        <div id="chatbox-messages"></div>
        <div id="chatbox-input-row">
            <form id="chatbox-form" class="d-flex gap-2 mb-0">
                <input type="text" id="chatbox-input" class="form-control form-control-sm"
                       placeholder="Nhập câu hỏi hóa học..." autocomplete="off">
                <button class="btn btn-primary btn-sm" type="submit">Gửi</button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const STORAGE_KEY = 'chemlearn_chat_history';
            const bubbleBtn = document.getElementById('chat-bubble-btn');
            const windowBox = document.getElementById('chatbox-window');
            const closeBtn = document.getElementById('chatbox-close-btn');
            const messagesEl = document.getElementById('chatbox-messages');
            const form = document.getElementById('chatbox-form');
            const input = document.getElementById('chatbox-input');
            const endpoint = '<?= app_url('chatbot/ask'); ?>';

            if (!bubbleBtn || !windowBox || !messagesEl || !form || !input) {
                return;
            }

            function loadHistory() {
                try {
                    const raw = localStorage.getItem(STORAGE_KEY);
                    return raw ? JSON.parse(raw) : [];
                } catch (error) {
                    return [];
                }
            }

            function saveHistory(history) {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
            }

            function escapeHtml(str) {
                return str.replace(/[&<>"']/g, function (ch) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    })[ch];
                });
            }

            function renderMessages() {
                const history = loadHistory();
                messagesEl.innerHTML = '';

                history.forEach(function (message) {
                    const div = document.createElement('div');
                    div.className = message.from === 'user' ? 'bubble-user' : 'bubble-bot';
                    div.innerHTML = escapeHtml(String(message.text || ''));
                    messagesEl.appendChild(div);
                });

                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function openChatWindow() {
                if (windowBox.style.display !== 'flex') {
                    windowBox.style.display = 'flex';
                    renderMessages();
                }
                input.focus();
            }

            bubbleBtn.addEventListener('click', function () {
                if (windowBox.style.display === 'none' || windowBox.style.display === '') {
                    openChatWindow();
                } else {
                    windowBox.style.display = 'none';
                }
            });

            closeBtn.addEventListener('click', function () {
                windowBox.style.display = 'none';
            });

            document.querySelectorAll('[data-open-chat]').forEach(function (trigger) {
                trigger.addEventListener('click', function (event) {
                    event.preventDefault();
                    openChatWindow();
                });
            });

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const text = input.value.trim();
                if (!text) {
                    return;
                }

                let history = loadHistory();
                history.push({from: 'user', text: text});
                saveHistory(history);
                renderMessages();
                input.value = '';

                const meta = document.querySelector('meta[name="csrf-token"]');
                const csrf = meta ? meta.getAttribute('content') : '';

                const formData = new FormData();
                formData.append('message', text);
                formData.append('csrf', csrf || '');

                fetch(endpoint, {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        return response.json();
                    })
                    .then(function (data) {
                        const reply = data && data.answer ? String(data.answer) : 'Có lỗi xảy ra, bạn thử lại sau nhé.';
                        history = loadHistory();
                        history.push({from: 'bot', text: reply});
                        saveHistory(history);
                        renderMessages();
                    })
                    .catch(function () {
                        history = loadHistory();
                        history.push({from: 'bot', text: 'Không kết nối được server.'});
                        saveHistory(history);
                        renderMessages();
                    });
            });

            renderMessages();
        })();
    </script>
    <!-- ========== HẾT CHATBOT BONG BÓNG ========== -->
</body>
</html>

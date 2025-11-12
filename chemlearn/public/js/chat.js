(function () {
    document.addEventListener('DOMContentLoaded', () => {
        // Nếu đã có bong bóng chat (do render thủ công) thì bỏ qua
        if (document.querySelector('.chat-bubble[data-offline-chat]')) {
            return;
        }

        // Tạo bong bóng chat AI ChemLearn và gắn vào cuối body
        const container = document.createElement('div');
        container.className = 'chat-bubble';
        container.setAttribute('data-offline-chat', '');

        const label = document.createElement('div');
        label.className = 'chat-bubble__label';
        label.textContent = 'Chat AI ChemLearn';

        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'chat-bubble__button shadow';
        toggleBtn.innerHTML = '<span aria-hidden="true">💬</span><span class="visually-hidden">Mở chat AI ChemLearn</span>';
        toggleBtn.setAttribute('aria-expanded', 'false');
        toggleBtn.setAttribute('aria-haspopup', 'dialog');

        const panel = document.createElement('div');
        panel.className = 'chat-bubble__panel shadow d-none';
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-modal', 'false');
        panel.setAttribute('aria-label', 'Cửa sổ chat AI ChemLearn');

        const header = document.createElement('div');
        header.className = 'chat-bubble__header d-flex justify-content-between align-items-center';
        header.innerHTML = '<span>Chat AI ChemLearn</span>';

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close btn-close-sm';
        closeBtn.setAttribute('aria-label', 'Đóng chat AI ChemLearn');
        header.appendChild(closeBtn);

        const chatWindow = document.createElement('div');
        chatWindow.className = 'chat-window';
        chatWindow.setAttribute('data-chat-offline-window', '');

        const welcome = document.createElement('div');
        welcome.className = 'chat-message chat-message--assistant';
        welcome.innerHTML = '
            <div class="chat-message__avatar">🤖</div>
            <div class="chat-message__content">
                <p class="mb-1">Xin chào! Mình là gia sư Hóa học ChemLearn. Bạn cần hỗ trợ kiến thức nào?</p>
            </div>
        ';
        chatWindow.appendChild(welcome);

        const form = document.createElement('form');
        form.className = 'chat-form';
        form.setAttribute('data-chat-offline-form', '');

        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group input-group-sm';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'message';
        input.required = true;
        input.placeholder = 'Nhập câu hỏi Hóa học...';
        input.autocomplete = 'off';
        input.className = 'form-control';

        const sendBtn = document.createElement('button');
        sendBtn.type = 'submit';
        sendBtn.className = 'btn btn-success';
        sendBtn.textContent = 'Gửi';

        inputGroup.appendChild(input);
        inputGroup.appendChild(sendBtn);
        form.appendChild(inputGroup);

        const errorBox = document.createElement('div');
        errorBox.className = 'text-danger small mt-2 d-none';
        errorBox.setAttribute('data-chat-offline-error', '');
        form.appendChild(errorBox);

        panel.appendChild(header);
        panel.appendChild(chatWindow);
        panel.appendChild(form);

        container.appendChild(label);
        container.appendChild(toggleBtn);
        container.appendChild(panel);

        document.body.appendChild(container);

        const conversation = [];
        let isSending = false;

        const openPanel = () => {
            panel.classList.remove('d-none');
            toggleBtn.setAttribute('aria-expanded', 'true');
            label.classList.add('d-none');
            input.focus();
            scrollToBottom();
        };

        const closePanel = () => {
            panel.classList.add('d-none');
            toggleBtn.setAttribute('aria-expanded', 'false');
            label.classList.remove('d-none');
        };

        const scrollToBottom = () => {
            chatWindow.scrollTop = chatWindow.scrollHeight;
        };

        const appendMessage = (role, message) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'chat-message' + (role === 'user' ? ' chat-message--user' : ' chat-message--assistant');

            const avatar = document.createElement('div');
            avatar.className = 'chat-message__avatar';
            avatar.textContent = role === 'user' ? '🙂' : '🤖';

            const content = document.createElement('div');
            content.className = 'chat-message__content';

            const paragraphs = String(message).trim().split(/\n\s*\n/);
            paragraphs.forEach((paragraph, index) => {
                const text = paragraph.trim();
                if (!text) {
                    return;
                }
                const p = document.createElement('p');
                p.className = index === paragraphs.length - 1 ? 'mb-0' : 'mb-2';
                // Giữ xuống dòng đơn trong cùng đoạn
                text.split(/\n+/).forEach((line, lineIndex, lines) => {
                    p.appendChild(document.createTextNode(line));
                    if (lineIndex < lines.length - 1) {
                        p.appendChild(document.createElement('br'));
                    }
                });
                content.appendChild(p);
            });

            if (!content.childNodes.length) {
                const fallback = document.createElement('p');
                fallback.className = 'mb-0';
                fallback.textContent = role === 'user' ? message : 'ChemLearn AI đang cập nhật phản hồi.';
                content.appendChild(fallback);
            }

            wrapper.appendChild(avatar);
            wrapper.appendChild(content);
            chatWindow.appendChild(wrapper);
            scrollToBottom();
        };

        const setError = (message) => {
            if (!message) {
                errorBox.textContent = '';
                errorBox.classList.add('d-none');
                return;
            }
            errorBox.textContent = message;
            errorBox.classList.remove('d-none');
        };

        const toggleLoading = (loading) => {
            isSending = loading;
            sendBtn.disabled = loading;
            sendBtn.textContent = loading ? 'Đang gửi...' : 'Gửi';
        };

        toggleBtn.addEventListener('click', (event) => {
            event.stopPropagation();
            if (panel.classList.contains('d-none')) {
                openPanel();
            } else {
                closePanel();
            }
        });

        closeBtn.addEventListener('click', (event) => {
            event.preventDefault();
            closePanel();
        });

        panel.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        document.addEventListener('click', () => {
            if (!panel.classList.contains('d-none')) {
                closePanel();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !panel.classList.contains('d-none')) {
                closePanel();
            }
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            if (isSending) {
                return;
            }

            const message = input.value.trim();
            if (!message) {
                return;
            }

            setError('');
            appendMessage('user', message);

            const userEntry = { role: 'user', content: message };
            conversation.push(userEntry);

            input.value = '';
            toggleLoading(true);

            const maxContext = 10;
            const historyForPayload = conversation
                .slice(Math.max(0, conversation.length - 1 - maxContext), conversation.length - 1)
                .map((item) => ({
                    role: item.role,
                    content: item.content,
                }));

            try {
                const response = await fetch('chat_ollama.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        message,
                        history: historyForPayload,
                    }),
                });

                if (!response.ok) {
                    throw new Error('Không thể kết nối tới AI ChemLearn.');
                }

                const payload = await response.json();
                if (!payload || typeof payload.reply !== 'string') {
                    throw new Error(payload && payload.error ? payload.error : 'Phản hồi không hợp lệ từ AI.');
                }

                const reply = payload.reply.trim();
                conversation.push({ role: 'assistant', content: reply });
                appendMessage('assistant', reply);
            } catch (error) {
                setError(error instanceof Error ? error.message : 'Đã xảy ra lỗi không xác định.');
                conversation.pop();
            } finally {
                toggleLoading(false);
            }
        });
    });
})();

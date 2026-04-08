document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo các phần tử giao diện Chat AI
    const aiChatWindow = document.getElementById('aiChatWindow');
    const aiChatToggle = document.getElementById('aiChatToggle');
    const aiChatClose = document.getElementById('aiChatClose');
    const aiChatBody = document.getElementById('aiChatBody');
    const aiChatInput = document.getElementById('aiChatInput');
    const aiChatSend = document.getElementById('aiChatSend');

    /**
     * Bật/Tắt cửa sổ chat khi nhấn vào nút biểu tượng AI.
     */
    if (aiChatToggle) {
        aiChatToggle.addEventListener('click', () => {
            aiChatWindow.classList.toggle('active');
            if (aiChatWindow.classList.contains('active')) {
                aiChatInput.focus(); // Tự động tập trung vào ô nhập liệu
            }
        });
    }

    /**
     * Đóng cửa sổ chat khi nhấn vào nút X.
     */
    if (aiChatClose) {
        aiChatClose.addEventListener('click', () => {
            aiChatWindow.classList.remove('active');
        });
    }

    /**
     * Hàm xử lý gửi tin nhắn của người dùng lên máy chủ AI.
     * Thực hiện các bước: Gửi -> Hiển thị trạng thái chờ -> Nhận phản hồi -> Hiển thị.
     */
    async function sendMessage() {
        const message = aiChatInput.value.trim();
        if (!message) return;

        // 1. Hiển thị tin nhắn của người dùng lên giao diện
        appendMessage('user', message);
        aiChatInput.value = '';

        // 2. Hiển thị trạng thái đang xử lý (Loading)
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'ai-message bg-light p-2 rounded-3 mb-2 small opacity-50';
        loadingDiv.innerText = 'Đang suy nghĩ...';
        aiChatBody.appendChild(loadingDiv);
        aiChatBody.scrollTop = aiChatBody.scrollHeight; // Cuộn xuống cuối

        try {
            // 3. Gọi API Backend để nhận phản hồi từ AI
            const response = await fetch(AI_CHAT_API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();
            
            // 4. Xóa trạng thái chờ sau khi có kết quả
            aiChatBody.removeChild(loadingDiv);

            if (data.reply) {
                appendMessage('ai', data.reply);
            } else {
                appendMessage('ai', 'Xin lỗi, có lỗi xảy ra khi kết nối với máy chủ AI.');
            }
        } catch (error) {
            // 5. Xử lý lỗi kết nối
            if (aiChatBody.contains(loadingDiv)) aiChatBody.removeChild(loadingDiv);
            appendMessage('ai', 'Lỗi kết nối mạng. Vui lòng thử lại sau.');
            console.error('Chat Error:', error);
        }
    }

    /**
     * Hàm hỗ trợ thêm tin nhắn vào khung chat.
     * @param {string} role 'user' (người dùng) hoặc 'ai' (trung tâm trợ giúp)
     * @param {string} text Nội dung tin nhắn
     */
    function appendMessage(role, text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = role === 'user' ? 'user-message p-2 rounded-3 mb-2 small' : 'ai-message bg-light p-2 rounded-3 mb-2 small';
        msgDiv.innerText = text;
        aiChatBody.appendChild(msgDiv);
        aiChatBody.scrollTop = aiChatBody.scrollHeight; // Tự động cuộn trang
    }

    // Các sự kiện kích hoạt gửi tin nhắn (Click nút hoặc nhấn Enter)
    if (aiChatSend) aiChatSend.addEventListener('click', sendMessage);
    if (aiChatInput) {
        aiChatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    }
});
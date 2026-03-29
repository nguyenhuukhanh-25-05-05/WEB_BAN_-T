document.addEventListener('DOMContentLoaded', function() {
    const aiChatWindow = document.getElementById('aiChatWindow');
    const aiChatToggle = document.getElementById('aiChatToggle');
    const aiChatClose = document.getElementById('aiChatClose');
    const aiChatBody = document.getElementById('aiChatBody');
    const aiChatInput = document.getElementById('aiChatInput');
    const aiChatSend = document.getElementById('aiChatSend');

    // Toggle Chat Window
    if (aiChatToggle) {
        aiChatToggle.addEventListener('click', () => {
            aiChatWindow.classList.toggle('active');
            if (aiChatWindow.classList.contains('active')) {
                aiChatInput.focus();
            }
        });
    }

    if (aiChatClose) {
        aiChatClose.addEventListener('click', () => {
            aiChatWindow.classList.remove('active');
        });
    }

    // Send Message Function
    async function sendMessage() {
        const message = aiChatInput.value.trim();
        if (!message) return;

        // Add user message to UI
        appendMessage('user', message);
        aiChatInput.value = '';

        // Add loading state
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'ai-message bg-light p-2 rounded-3 mb-2 small opacity-50';
        loadingDiv.innerText = 'Đang suy nghĩ...';
        aiChatBody.appendChild(loadingDiv);
        aiChatBody.scrollTop = aiChatBody.scrollHeight;

        try {
            // Call Backend API
            const response = await fetch(AI_CHAT_API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();
            
            // Remove loading
            aiChatBody.removeChild(loadingDiv);

            if (data.reply) {
                appendMessage('ai', data.reply);
            } else {
                appendMessage('ai', 'Xin lỗi, có lỗi xảy ra khi kết nối với máy chủ AI.');
            }
        } catch (error) {
            aiChatBody.removeChild(loadingDiv);
            appendMessage('ai', 'Lỗi kết nối mạng. Vui lòng thử lại sau.');
            console.error('Chat Error:', error);
        }
    }

    function appendMessage(role, text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = role === 'user' ? 'user-message p-2 rounded-3 mb-2 small' : 'ai-message bg-light p-2 rounded-3 mb-2 small';
        msgDiv.innerText = text;
        aiChatBody.appendChild(msgDiv);
        aiChatBody.scrollTop = aiChatBody.scrollHeight;
    }

    // Event Listeners
    aiChatSend.addEventListener('click', sendMessage);
    aiChatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});
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

        // Add loading state (Typing Indicator)
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'ai-message bg-light p-3 rounded-4 mb-3 small typing-indicator';
        loadingDiv.innerHTML = '<span></span><span></span><span></span>';
        aiChatBody.appendChild(loadingDiv);
        aiChatBody.scrollTop = aiChatBody.scrollHeight;

        try {
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
            if (aiChatBody.contains(loadingDiv)) aiChatBody.removeChild(loadingDiv);
            appendMessage('ai', 'Lỗi kết nối mạng. Vui lòng thử lại sau.');
            console.error('Chat Error:', error);
        }
    }

    function appendMessage(role, text) {
        const msgDiv = document.createElement('div');
        // Better classes for premium look
        if (role === 'user') {
            msgDiv.className = 'user-message p-3 rounded-4 mb-3 small shadow-sm animate-slide-in-right';
        } else {
            msgDiv.className = 'ai-message bg-light p-3 rounded-4 mb-3 small shadow-sm animate-slide-in-left';
        }
        
        // Simple newline to <br> conversion and basic security
        const safeText = text.replace(/&/g, "&amp;")
                             .replace(/</g, "&lt;")
                             .replace(/>/g, "&gt;")
                             .replace(/"/g, "&quot;")
                             .replace(/'/g, "&#039;")
                             .replace(/\n/g, "<br>");
        
        msgDiv.innerHTML = safeText;
        aiChatBody.appendChild(msgDiv);
        aiChatBody.scrollTop = aiChatBody.scrollHeight;
    }

    // Event Listeners
    aiChatSend.addEventListener('click', sendMessage);
    aiChatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});
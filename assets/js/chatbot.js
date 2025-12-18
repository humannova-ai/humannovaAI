/**
 * Human Nova AI - Assistant Professionnel avec OpenAI
 * Version 3.1 - Intégration OpenAI GPT améliorée
 */

class NovaBot {
    constructor() {
        this.isOpen = false;
        this.isTyping = false;
        this.conversationContext = [];
        this.apiEndpoint = this.getApiEndpoint();
        this.init();
    }

    getApiEndpoint() {
        const currentPath = window.location.pathname;
        let basePath = '';
        
        if (currentPath.includes('/views/admin/') || currentPath.includes('/views/front/')) {
            basePath = '../../';
        } else if (currentPath.includes('/views/')) {
            basePath = '../';
        }
        
        return basePath + 'api/openai.php';
    }

    init() {
        this.createChatWidget();
        this.bindEvents();
        this.addWelcomeMessage();
        console.log('Nova AI initialized. API endpoint:', this.apiEndpoint);
    }

    createChatWidget() {
        const chatWidget = document.createElement('div');
        chatWidget.id = 'nova-chatbot';
        chatWidget.innerHTML = `
            <div class="nova-toggle" id="novaToggle">
                <div class="nova-toggle-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M8 10.5C8.55228 10.5 9 10.0523 9 9.5C9 8.94772 8.55228 8.5 8 8.5C7.44772 8.5 7 8.94772 7 9.5C7 10.0523 7.44772 10.5 8 10.5Z" fill="currentColor"/>
                        <path d="M16 10.5C16.5523 10.5 17 10.0523 17 9.5C17 8.94772 16.5523 8.5 16 8.5C15.4477 8.5 15 8.94772 15 9.5C15 10.0523 15.4477 10.5 16 10.5Z" fill="currentColor"/>
                        <path d="M12 18C15 18 17 15.5 17 13H7C7 15.5 9 18 12 18Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="nova-toggle-pulse"></div>
                <div class="nova-badge" id="novaBadge">1</div>
            </div>

            <div class="nova-chat-window" id="novaChatWindow">
                <div class="nova-header">
                    <div class="nova-header-info">
                        <div class="nova-avatar">
                            <div class="nova-avatar-glow"></div>
                            <span class="nova-avatar-icon">🤖</span>
                        </div>
                        <div class="nova-header-text">
                            <h3>Nova AI <span class="nova-pro-badge">GPT</span></h3>
                            <span class="nova-status">
                                <span class="nova-status-dot"></span>
                                Assistant Intelligent - Répond à tout
                            </span>
                        </div>
                    </div>
                    <button class="nova-close" id="novaClose">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

                <div class="nova-messages" id="novaMessages"></div>

                <div class="nova-suggestions" id="novaSuggestions">
                    <button class="nova-suggestion" data-message="Quelle est la capitale de la France ?">🌍 Culture</button>
                    <button class="nova-suggestion" data-message="Explique-moi la théorie de la relativité">🔬 Science</button>
                    <button class="nova-suggestion" data-message="Comment créer un événement ?">📅 Événements</button>
                    <button class="nova-suggestion" data-message="Raconte-moi une blague">😄 Humour</button>
                </div>

                <div class="nova-input-container">
                    <input type="text" class="nova-input" id="novaInput" placeholder="Posez n'importe quelle question...">
                    <button class="nova-send" id="novaSend">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M22 2L11 13M22 2L15 22L11 13M22 2L2 9L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(chatWidget);
        this.addStyles();
    }

    addStyles() {
        const styles = document.createElement('style');
        styles.textContent = `
            #nova-chatbot {
                position: fixed;
                bottom: 30px;
                right: 30px;
                z-index: 10000;
                font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            }

            .nova-toggle {
                width: 68px;
                height: 68px;
                border-radius: 50%;
                background: linear-gradient(135deg, #00ffff, #9945ff);
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 8px 32px rgba(0, 255, 255, 0.4), 0 0 60px rgba(153, 69, 255, 0.3);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                position: relative;
            }

            .nova-toggle:hover {
                transform: scale(1.1) rotate(10deg);
            }

            .nova-toggle-icon {
                width: 34px;
                height: 34px;
                color: #000;
                z-index: 2;
            }

            .nova-toggle-icon svg {
                width: 100%;
                height: 100%;
            }

            .nova-toggle-pulse {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 50%;
                background: linear-gradient(135deg, #00ffff, #9945ff);
                animation: novaPulse 2s infinite;
                z-index: 1;
            }

            @keyframes novaPulse {
                0% { transform: scale(1); opacity: 0.5; }
                100% { transform: scale(1.6); opacity: 0; }
            }

            .nova-badge {
                position: absolute;
                top: -5px;
                right: -5px;
                width: 26px;
                height: 26px;
                background: linear-gradient(135deg, #ff3333, #ff0066);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
                color: #fff;
                animation: novaBounce 1s ease infinite;
                border: 2px solid #0a0a0a;
            }

            .nova-badge.hidden { display: none; }

            @keyframes novaBounce {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.2); }
            }

            .nova-chat-window {
                position: absolute;
                bottom: 85px;
                right: 0;
                width: 400px;
                height: 550px;
                background: linear-gradient(180deg, #141414 0%, #0a0a0a 100%);
                border-radius: 24px;
                box-shadow: 0 25px 80px rgba(0, 0, 0, 0.6), 0 0 60px rgba(0, 255, 255, 0.15);
                border: 1px solid rgba(0, 255, 255, 0.2);
                display: flex;
                flex-direction: column;
                overflow: hidden;
                opacity: 0;
                visibility: hidden;
                transform: translateY(20px) scale(0.95);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .nova-chat-window.open {
                opacity: 1;
                visibility: visible;
                transform: translateY(0) scale(1);
            }

            .nova-header {
                background: linear-gradient(135deg, rgba(0, 255, 255, 0.08), rgba(153, 69, 255, 0.08));
                padding: 18px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            }

            .nova-header-info {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .nova-avatar {
                width: 48px;
                height: 48px;
                border-radius: 14px;
                background: linear-gradient(135deg, #00ffff, #9945ff);
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
            }

            .nova-avatar-icon {
                font-size: 24px;
                z-index: 2;
            }

            .nova-avatar-glow {
                position: absolute;
                width: 100%;
                height: 100%;
                border-radius: 14px;
                background: linear-gradient(135deg, #00ffff, #9945ff);
                filter: blur(10px);
                opacity: 0.5;
                z-index: -1;
            }

            .nova-header-text h3 {
                color: #fff;
                font-size: 17px;
                font-weight: 700;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .nova-pro-badge {
                background: linear-gradient(135deg, #10b981, #059669);
                color: #fff;
                font-size: 9px;
                font-weight: 800;
                padding: 3px 8px;
                border-radius: 4px;
            }

            .nova-status {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 12px;
                color: #00ff88;
                margin-top: 3px;
            }

            .nova-status-dot {
                width: 8px;
                height: 8px;
                background: #00ff88;
                border-radius: 50%;
                animation: novaStatusPulse 1.5s ease infinite;
            }

            @keyframes novaStatusPulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.5; }
            }

            .nova-close {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                color: #fff;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s;
            }

            .nova-close:hover {
                background: rgba(255, 51, 51, 0.2);
                border-color: #ff3333;
                color: #ff3333;
            }

            .nova-close svg {
                width: 18px;
                height: 18px;
            }

            .nova-messages {
                flex: 1;
                overflow-y: auto;
                padding: 18px;
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .nova-messages::-webkit-scrollbar {
                width: 5px;
            }

            .nova-messages::-webkit-scrollbar-thumb {
                background: rgba(0, 255, 255, 0.3);
                border-radius: 3px;
            }

            .nova-message {
                display: flex;
                gap: 10px;
                animation: novaMessageIn 0.3s ease;
            }

            @keyframes novaMessageIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .nova-message.user {
                flex-direction: row-reverse;
            }

            .nova-message-avatar {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                flex-shrink: 0;
            }

            .nova-message.bot .nova-message-avatar {
                background: linear-gradient(135deg, #00ffff, #9945ff);
            }

            .nova-message.user .nova-message-avatar {
                background: linear-gradient(135deg, #ff9500, #ff3333);
            }

            .nova-message-content {
                max-width: 75%;
                padding: 14px 18px;
                border-radius: 18px;
                font-size: 14px;
                line-height: 1.5;
            }

            .nova-message.bot .nova-message-content {
                background: linear-gradient(135deg, rgba(0, 255, 255, 0.08), rgba(153, 69, 255, 0.05));
                border: 1px solid rgba(0, 255, 255, 0.15);
                color: #fff;
                border-bottom-left-radius: 4px;
            }

            .nova-message.bot .nova-message-content strong {
                color: #00ffff;
            }

            .nova-message.user .nova-message-content {
                background: linear-gradient(135deg, #00ffff, #00a8ff);
                color: #000;
                font-weight: 500;
                border-bottom-right-radius: 4px;
            }

            .nova-typing {
                display: flex;
                align-items: center;
                gap: 5px;
                padding: 14px 18px;
            }

            .nova-typing span {
                width: 8px;
                height: 8px;
                background: linear-gradient(135deg, #00ffff, #9945ff);
                border-radius: 50%;
                animation: novaTyping 1.4s ease-in-out infinite;
            }

            .nova-typing span:nth-child(2) { animation-delay: 0.2s; }
            .nova-typing span:nth-child(3) { animation-delay: 0.4s; }

            @keyframes novaTyping {
                0%, 100% { transform: translateY(0); opacity: 0.5; }
                50% { transform: translateY(-6px); opacity: 1; }
            }

            .nova-suggestions {
                padding: 10px 18px;
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
            }

            .nova-suggestion {
                padding: 8px 14px;
                border-radius: 20px;
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.08);
                color: #a0a0a0;
                font-size: 12px;
                cursor: pointer;
                transition: all 0.3s;
            }

            .nova-suggestion:hover {
                background: rgba(0, 255, 255, 0.1);
                border-color: rgba(0, 255, 255, 0.3);
                color: #00ffff;
            }

            .nova-input-container {
                padding: 14px 18px;
                display: flex;
                gap: 10px;
                background: rgba(0, 0, 0, 0.4);
                border-top: 1px solid rgba(255, 255, 255, 0.08);
            }

            .nova-input {
                flex: 1;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 22px;
                padding: 12px 20px;
                color: #fff;
                font-size: 14px;
                outline: none;
                transition: all 0.3s;
            }

            .nova-input::placeholder {
                color: #505050;
            }

            .nova-input:focus {
                border-color: rgba(0, 255, 255, 0.5);
                box-shadow: 0 0 20px rgba(0, 255, 255, 0.1);
            }

            .nova-send {
                width: 46px;
                height: 46px;
                border-radius: 50%;
                background: linear-gradient(135deg, #00ffff, #9945ff);
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s;
                color: #000;
            }

            .nova-send:hover {
                transform: scale(1.08);
            }

            .nova-send:disabled {
                opacity: 0.5;
                cursor: not-allowed;
                transform: none;
            }

            .nova-send svg {
                width: 18px;
                height: 18px;
            }

            .nova-message-content code {
                background: rgba(0, 0, 0, 0.4);
                padding: 2px 6px;
                border-radius: 4px;
                font-family: monospace;
                color: #00ffff;
            }

            @media (max-width: 480px) {
                #nova-chatbot { bottom: 15px; right: 15px; }
                .nova-chat-window {
                    width: calc(100vw - 30px);
                    height: calc(100vh - 100px);
                    bottom: 80px;
                }
                .nova-toggle { width: 56px; height: 56px; }
            }
        `;
        document.head.appendChild(styles);
    }

    bindEvents() {
        document.getElementById('novaToggle').addEventListener('click', () => this.toggleChat());
        document.getElementById('novaClose').addEventListener('click', () => this.closeChat());
        document.getElementById('novaSend').addEventListener('click', () => this.sendMessage());
        document.getElementById('novaInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });

        document.querySelectorAll('.nova-suggestion').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('novaInput').value = btn.dataset.message;
                this.sendMessage();
            });
        });
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        const chatWindow = document.getElementById('novaChatWindow');
        const badge = document.getElementById('novaBadge');

        if (this.isOpen) {
            chatWindow.classList.add('open');
            badge.classList.add('hidden');
            document.getElementById('novaInput').focus();
        } else {
            chatWindow.classList.remove('open');
        }
    }

    closeChat() {
        this.isOpen = false;
        document.getElementById('novaChatWindow').classList.remove('open');
    }

    addWelcomeMessage() {
        setTimeout(() => {
            this.addBotMessage("👋 Bonjour ! Je suis **Nova AI**, votre assistant intelligent.\n\nJe peux répondre à **toutes vos questions** :\n• 🌍 Culture générale\n• 🔬 Sciences\n• 💻 Technologie\n• 📅 Gestion des événements\n• 🎨 Art et créativité\n\n**Posez-moi n'importe quelle question !** 🚀");
        }, 500);
    }

    async sendMessage() {
        const input = document.getElementById('novaInput');
        const sendBtn = document.getElementById('novaSend');
        const message = input.value.trim();

        if (!message || this.isTyping) return;

        this.addUserMessage(message);
        input.value = '';
        sendBtn.disabled = true;
        this.showTyping();

        try {
            console.log('Sending to:', this.apiEndpoint);
            
            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    message: message,
                    history: this.conversationContext.slice(-6)
                })
            });

            console.log('Response status:', response.status);
            
            const text = await response.text();
            console.log('Response text:', text);
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid JSON response: ' + text.substring(0, 100));
            }

            this.hideTyping();

            if (data.success && data.response) {
                this.addBotMessage(data.response);
                this.conversationContext.push({ role: 'user', content: message });
                this.conversationContext.push({ role: 'assistant', content: data.response });
            } else {
                const errorMsg = data.error || 'Erreur inconnue';
                console.error('API Error:', errorMsg);
                this.addBotMessage("⚠️ **Erreur de connexion à l'IA**\n\n" + errorMsg + "\n\n*Vérifiez la configuration de l'API OpenAI.*");
            }
        } catch (error) {
            this.hideTyping();
            console.error('Fetch error:', error);
            this.addBotMessage("❌ **Erreur de connexion**\n\n" + error.message + "\n\n*Vérifiez que le serveur PHP fonctionne correctement.*");
        }

        sendBtn.disabled = false;
    }

    addUserMessage(text) {
        const messagesContainer = document.getElementById('novaMessages');
        const messageEl = document.createElement('div');
        messageEl.className = 'nova-message user';
        messageEl.innerHTML = `
            <div class="nova-message-avatar">👤</div>
            <div class="nova-message-content">${this.escapeHtml(text)}</div>
        `;
        messagesContainer.appendChild(messageEl);
        this.scrollToBottom();
    }

    addBotMessage(text) {
        const messagesContainer = document.getElementById('novaMessages');
        const messageEl = document.createElement('div');
        messageEl.className = 'nova-message bot';
        messageEl.innerHTML = `
            <div class="nova-message-avatar">🤖</div>
            <div class="nova-message-content">${this.formatMessage(text)}</div>
        `;
        messagesContainer.appendChild(messageEl);
        this.scrollToBottom();
    }

    showTyping() {
        this.isTyping = true;
        const messagesContainer = document.getElementById('novaMessages');
        const typingEl = document.createElement('div');
        typingEl.className = 'nova-message bot';
        typingEl.id = 'novaTyping';
        typingEl.innerHTML = `
            <div class="nova-message-avatar">🤖</div>
            <div class="nova-message-content">
                <div class="nova-typing">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        messagesContainer.appendChild(typingEl);
        this.scrollToBottom();
    }

    hideTyping() {
        this.isTyping = false;
        const typingEl = document.getElementById('novaTyping');
        if (typingEl) typingEl.remove();
    }

    scrollToBottom() {
        const container = document.getElementById('novaMessages');
        container.scrollTop = container.scrollHeight;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatMessage(text) {
        return text
            .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
            .replace(/`([^`]+)`/g, '<code>$1</code>')
            .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
            .replace(/\*([^*]+)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    window.novaBot = new NovaBot();
});

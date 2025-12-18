/**
 * Pro Manage AI Chatbot Widget
 * Interactive chatbot for innovation and investment questions
 */

class ChatbotWidget {
    constructor() {
        this.widget = null;
        this.messages = null;
        this.input = null;
        this.sendBtn = null;
        this.toggleBtn = null;
        this.isOpen = false;
        this.isTyping = false;

        this.init();
    }

    init() {
        this.createWidget();
        this.bindEvents();
        this.loadGreeting();
    }

    createWidget() {
        // Create toggle button
        this.toggleBtn = document.createElement('button');
        this.toggleBtn.className = 'chatbot-toggle';
        this.toggleBtn.innerHTML = '💡';
        this.toggleBtn.title = 'Chat with Pro Manage AI Assistant';
        document.body.appendChild(this.toggleBtn);

        // Create main widget
        this.widget = document.createElement('div');
        this.widget.className = 'chatbot-widget';
        this.widget.innerHTML = `
            <div class="chatbot-header">
                <h3>💡 Pro Manage AI Assistant</h3>
                <button class="chatbot-close">&times;</button>
            </div>
            <div class="chatbot-messages"></div>
            <div class="chatbot-typing">L'assistant tape...</div>
            <div class="chatbot-input-area">
                <input type="text" class="chatbot-input" placeholder="Posez votre question sur l'innovation...">
                <button class="chatbot-send">Envoyer</button>
            </div>
        `;
        document.body.appendChild(this.widget);

        // Get elements
        this.messages = this.widget.querySelector('.chatbot-messages');
        this.input = this.widget.querySelector('.chatbot-input');
        this.sendBtn = this.widget.querySelector('.chatbot-send');
        this.closeBtn = this.widget.querySelector('.chatbot-close');
        this.typingIndicator = this.widget.querySelector('.chatbot-typing');
    }

    bindEvents() {
        // Toggle widget
        this.toggleBtn.addEventListener('click', () => this.toggle());

        // Close widget
        this.closeBtn.addEventListener('click', () => this.close());

        // Send message
        this.sendBtn.addEventListener('click', () => this.sendMessage());

        // Enter key
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });

        // Click outside to close
        document.addEventListener('click', (e) => {
            if (!this.widget.contains(e.target) && !this.toggleBtn.contains(e.target) && this.isOpen) {
                this.close();
            }
        });
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        this.widget.classList.add('active');
        this.isOpen = true;
        this.input.focus();
    }

    close() {
        this.widget.classList.remove('active');
        this.isOpen = false;
    }

    async loadGreeting() {
        try {
            const response = await fetch('assets/chatbot/chatbot-controller.php?action=getGreeting');
            const data = await response.json();

            if (data.success) {
                this.addMessage(data.response, 'bot');
            }
        } catch (error) {
            console.error('Error loading greeting:', error);
            this.addMessage('Salut! 👋 Je suis l\'assistant Pro Manage AI. Comment puis-je vous aider?', 'bot');
        }
    }

    async sendMessage() {
        const message = this.input.value.trim();
        if (!message || this.isTyping) return;

        // Add user message
        this.addMessage(message, 'user');
        this.input.value = '';

        // Show typing indicator
        this.showTyping();

        try {
            const response = await fetch(`assets/chatbot/chatbot-controller.php?action=getResponse&message=${encodeURIComponent(message)}`);
            const data = await response.json();

            // Hide typing indicator
            this.hideTyping();

            if (data.success) {
                this.addMessage(data.response, 'bot');
            } else {
                this.addMessage('Désolé, une erreur s\'est produite. Veuillez réessayer.', 'bot');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.hideTyping();
            this.addMessage('Désolé, je ne peux pas répondre pour le moment. Veuillez réessayer plus tard.', 'bot');
        }
    }

    addMessage(text, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${type}`;
        messageDiv.textContent = text;
        this.messages.appendChild(messageDiv);

        // Scroll to bottom
        this.messages.scrollTop = this.messages.scrollHeight;
    }

    showTyping() {
        this.isTyping = true;
        this.typingIndicator.style.display = 'block';
        this.messages.scrollTop = this.messages.scrollHeight;
    }

    hideTyping() {
        this.isTyping = false;
        this.typingIndicator.style.display = 'none';
    }
}

// Initialize chatbot when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ChatbotWidget();
});

// Load CSS
const link = document.createElement('link');
link.rel = 'stylesheet';
link.href = 'assets/chatbot/chatbot-widget.css';
document.head.appendChild(link);
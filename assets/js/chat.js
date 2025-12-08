// assets/js/chat.js - Human-like AI Chatbot with ML

let chatHistory = [];
let conversationContext = {
    askedForRecommendation: false,
    currentEmotion: null,
    userName: null,
    lastTopics: []
};

// Initialize chat
document.addEventListener('DOMContentLoaded', function() {
    initializeChat();
});

// Initialize chat with welcome message
function initializeChat() {
    const welcomeMessages = [
        "Hey there! 👋 I'm here to chat with you. How's your day going?",
        "Hi! 😊 What's on your mind today?",
        "Hello! 🎵 How are you feeling?",
        "Hey! Good to see you! What's up? 💜"
    ];
    
    const welcomeMessage = {
        role: 'assistant',
        content: welcomeMessages[Math.floor(Math.random() * welcomeMessages.length)]
    };
    
    chatHistory.push(welcomeMessage);
    displayChatMessage(welcomeMessage);
}

// Send message to AI (ML-Powered)
async function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message to chat
    const userMessage = { role: 'user', content: message };
    chatHistory.push(userMessage);
    displayChatMessage(userMessage);
    
    // Clear input
    input.value = '';
    
    // Show typing indicator
    showTypingIndicator();
    
    try {
        // Call ML-powered API
        const response = await fetch('api/ml-chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: message,
                history: chatHistory.slice(-10)
            })
        });
        
        const data = await response.json();
        
        removeTypingIndicator();
        
        if (data.success) {
            // Display ML-powered response
            const assistantMessage = {
                role: 'assistant',
                content: data.response,
                emotion: data.detectedEmotion,
                confidence: data.confidence,
                songs: data.recommendedSongs,
                mlPowered: data.mlPowered
            };
            
            displayChatMessage(assistantMessage);
            chatHistory.push(assistantMessage);
            
            // Show ML indicator if powered by ML
            if (data.mlPowered) {
                console.log('🤖 ML Model:', data.model);
                console.log('📊 Confidence:', data.confidence + '%');
                console.log('😊 Detected:', data.detectedEmotion);
            }
        } else {
            // Fallback to local response
            const response = generateHumanResponse(message);
            displayChatMessage(response);
            chatHistory.push(response);
        }
    } catch (error) {
        console.error('ML Chat Error:', error);
        removeTypingIndicator();
        
        // Fallback to local response
        const response = generateHumanResponse(message);
        displayChatMessage(response);
        chatHistory.push(response);
    }
}

// Display chat message (Enhanced for ML)
function displayChatMessage(message) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${message.role}`;
    
    let messageHTML = `<div class="message-bubble">${message.content}`;
    
    // Add ML confidence indicator
    if (message.mlPowered && message.confidence) {
        const confidenceColor = message.confidence >= 80 ? '#22c55e' : 
                               message.confidence >= 60 ? '#eab308' : '#ef4444';
        messageHTML += `
            <div style="margin-top: 0.5rem; font-size: 0.75rem; opacity: 0.7;">
                🤖 ML Confidence: <span style="color: ${confidenceColor}; font-weight: 600;">${message.confidence}%</span>
            </div>
        `;
    }
    
    // Add song recommendations if present
    if (message.songs && message.songs.length > 0) {
        messageHTML += `<div style="margin-top: 1rem;">`;
        message.songs.forEach((song, index) => {
            messageHTML += `
                <div class="chat-song-recommendation" style="margin-bottom: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <span style="font-size: 1.5rem;">${song.cover}</span>
                        <div style="flex: 1;">
                            <div class="chat-song-title">${song.title}</div>
                            <div class="chat-song-artist">${song.artist} • ${song.duration}</div>
                        </div>
                    </div>
                    <button class="play-recommended-btn" onclick="playRecommendedSong(${song.id})">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        Play Now
                    </button>
                </div>
            `;
        });
        messageHTML += `</div>`;
    }
    
    // Legacy song recommendation support
    if (message.song && !message.songs) {
        messageHTML += `
            <div class="chat-song-recommendation">
                <div class="chat-song-title">${message.song.title}</div>
                <div class="chat-song-artist">${message.song.artist}</div>
                <button class="play-recommended-btn" onclick="playRecommendedSong(${message.song.id})">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    Play Now
                </button>
            </div>
        `;
    }
    
    messageHTML += `</div>`;
    messageDiv.innerHTML = messageHTML;
    
    messagesContainer.appendChild(messageDiv);
    scrollChatToBottom();
}

// Generate human-like response (fallback)
function generateHumanResponse(userMessage) {
    const lowerMessage = userMessage.toLowerCase();
    
    // Check if asking for music
    const isAskingForMusic = /recommend|suggest|play|find|song|music|listen|what should i|give me/i.test(lowerMessage);
    
    // Simple greeting
    if (isGreeting(lowerMessage)) {
        const greetings = [
            "Hey! 👋 How's it going?",
            "Hi there! 😊 What's up?",
            "Hello! How are you doing today?",
            "Hey! Good to see you! What's on your mind?"
        ];
        return {
            role: 'assistant',
            content: greetings[Math.floor(Math.random() * greetings.length)]
        };
    }
    
    // How are you questions
    if (/how are you|how r u|hows it going|whats up/i.test(lowerMessage)) {
        const responses = [
            "I'm doing great, thanks! 😊 How about you?",
            "I'm good! What about you - how's your day?",
            "Pretty good! How are YOU doing?",
            "I'm well! What brings you here today?"
        ];
        return {
            role: 'assistant',
            content: responses[Math.floor(Math.random() * responses.length)]
        };
    }
    
    // If asking for music, detect emotion and recommend
    if (isAskingForMusic) {
        const emotion = detectEmotion(lowerMessage);
        const responses = {
            sad: "I can help with that. Let me find some comforting songs for you. 💙",
            happy: "Awesome! Let me find some upbeat tracks for you! 😊",
            calm: "Sure! I'll find some relaxing music for you. 😌",
            energetic: "Let's go! I'll find some high-energy tracks! ⚡"
        };
        return {
            role: 'assistant',
            content: responses[emotion] || "Sure! What kind of mood are you in? Happy, sad, calm, or energetic?"
        };
    }
    
    // Just chatting - detect emotion and respond conversationally
    const emotion = detectEmotion(lowerMessage);
    if (emotion) {
        const responses = {
            sad: [
                "I'm sorry you're feeling down. 😔 Want to talk about what's bothering you?",
                "That sounds tough. I'm here to listen if you want to share.",
                "I hear you. What's been going on?"
            ],
            happy: [
                "That's awesome! 😊 What's making you so happy?",
                "I love your energy! What happened?",
                "Yes! What's got you in such a great mood?"
            ],
            calm: [
                "That sounds peaceful! 😌 What are you up to?",
                "Nice! Are you relaxing or meditating?",
                "Sounds like you're in a good headspace. Doing anything special?"
            ],
            energetic: [
                "Woah! I can feel that energy! ⚡ What are you doing?",
                "You're pumped up! What's the plan?",
                "Love the enthusiasm! What's got you so hyped?"
            ]
        };
        const emotionResponses = responses[emotion];
        return {
            role: 'assistant',
            content: emotionResponses[Math.floor(Math.random() * emotionResponses.length)]
        };
    }
    
    // Generic conversational responses
    const genericResponses = [
        "That's interesting! Tell me more. 🤔",
        "I see. How do you feel about that?",
        "I'm listening. Want to share more?",
        "Interesting! What else is on your mind?",
        "I hear you. What happened?"
    ];
    
    return {
        role: 'assistant',
        content: genericResponses[Math.floor(Math.random() * genericResponses.length)]
    };
}

// Helper functions
function isGreeting(msg) {
    return msg.match(/^(hi|hey|hello|sup|yo)\b/);
}

function detectEmotion(text) {
    const emotions = {
        sad: ['sad', 'down', 'depressed', 'lonely', 'hurt'],
        happy: ['happy', 'joy', 'excited', 'great', 'amazing'],
        calm: ['calm', 'peace', 'relax', 'chill', 'zen'],
        energetic: ['energy', 'workout', 'pump', 'party', 'active']
    };
    
    for (const [emotion, keywords] of Object.entries(emotions)) {
        if (keywords.some(word => text.includes(word))) {
            return emotion;
        }
    }
    return null;
}

// Show typing indicator
function showTypingIndicator() {
    const messagesContainer = document.getElementById('chatMessages');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'chat-message assistant';
    typingDiv.id = 'typingIndicator';
    typingDiv.innerHTML = `
        <div class="message-bubble">
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
    `;
    messagesContainer.appendChild(typingDiv);
    scrollChatToBottom();
}

// Remove typing indicator
function removeTypingIndicator() {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

// Scroll chat to bottom
function scrollChatToBottom() {
    const messagesContainer = document.getElementById('chatMessages');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Play recommended song from chat
function playRecommendedSong(songId) {
    const song = allSongs.find(s => s.id === songId);
    if (song) {
        loadSongInPlayer(song);
        
        // Add confirmation message
        const confirmMessage = {
            role: 'assistant',
            content: "Great choice! 🎵 Enjoy the music! Let me know what you think! 😊"
        };
        chatHistory.push(confirmMessage);
        displayChatMessage(confirmMessage);
    }
}
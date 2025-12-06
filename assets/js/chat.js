// assets/js/chat.js - Human-like AI Chatbot

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
        "Hey there! 👋 I'm here to chat with you about music, moods, or just life in general. What's on your mind today?",
        "Hi! 😊 How's your day going? I'm here if you want to talk about anything - music, feelings, or just chat!",
        "Hello! 🎵 I love talking about music and getting to know people. How are you feeling today?",
        "Hey! Welcome to HearYou! 💜 I'm not just a music bot - I'm here to have a real conversation with you. What's up?"
    ];
    
    const welcomeMessage = {
        role: 'assistant',
        content: welcomeMessages[Math.floor(Math.random() * welcomeMessages.length)]
    };
    
    chatHistory.push(welcomeMessage);
    displayChatMessage(welcomeMessage);
}

// Send message to AI
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
    
    // Process message
    setTimeout(() => {
        const response = generateHumanResponse(message);
        removeTypingIndicator();
        displayChatMessage(response);
        chatHistory.push(response);
    }, 1000 + Math.random() * 1000); // Random delay for human feel
}

// Generate human-like response
function generateHumanResponse(userMessage) {
    const lowerMessage = userMessage.toLowerCase();
    
    // Detect user name
    if (!conversationContext.userName && (lowerMessage.includes('my name is') || lowerMessage.includes("i'm "))) {
        const nameMatch = userMessage.match(/(?:my name is|i'm|im)\s+([a-zA-Z]+)/i);
        if (nameMatch) {
            conversationContext.userName = nameMatch[1];
            return {
                role: 'assistant',
                content: `Nice to meet you, ${conversationContext.userName}! 😊 That's a great name! So, what brings you to HearYou today?`
            };
        }
    }
    
    // Greetings
    if (isGreeting(lowerMessage)) {
        return generateGreetingResponse();
    }
    
    // How are you / feelings check
    if (isAskingAboutBot(lowerMessage)) {
        return generateBotFeelingsResponse();
    }
    
    // User expressing emotions
    const emotion = detectEmotion(lowerMessage);
    if (emotion) {
        conversationContext.currentEmotion = emotion;
        return generateEmotionResponse(emotion, lowerMessage);
    }
    
    // Asking for music recommendation
    if (isAskingForMusic(lowerMessage)) {
        conversationContext.askedForRecommendation = true;
        return generateMusicRecommendation();
    }
    
    // Talking about music in general
    if (isTalkingAboutMusic(lowerMessage)) {
        return generateMusicConversation(lowerMessage);
    }
    
    // Thank you
    if (lowerMessage.includes('thank') || lowerMessage.includes('thanks')) {
        return generateThankYouResponse();
    }
    
    // Goodbye
    if (isGoodbye(lowerMessage)) {
        return generateGoodbyeResponse();
    }
    
    // Yes/No responses
    if (lowerMessage === 'yes' || lowerMessage === 'yeah' || lowerMessage === 'sure') {
        if (conversationContext.askedForRecommendation) {
            return generateMusicRecommendation();
        }
        return {
            role: 'assistant',
            content: "Awesome! 😊 What would you like to know or talk about?"
        };
    }
    
    if (lowerMessage === 'no' || lowerMessage === 'nah' || lowerMessage === 'not really') {
        conversationContext.askedForRecommendation = false;
        return {
            role: 'assistant',
            content: "No worries! I'm here to chat about anything. What's on your mind? 💭"
        };
    }
    
    // General conversation
    return generateGeneralResponse(lowerMessage);
}

// Helper functions for detection
function isGreeting(msg) {
    return msg.match(/^(hi|hey|hello|sup|yo|greetings|good morning|good afternoon|good evening)\b/);
}

function isAskingAboutBot(msg) {
    return msg.includes('how are you') || 
           msg.includes('how r u') ||
           msg.includes("what's up") ||
           msg.includes('whats up') ||
           msg.includes('hows it going');
}

function isAskingForMusic(msg) {
    return (msg.includes('recommend') && msg.includes('song')) ||
           (msg.includes('suggest') && msg.includes('music')) ||
           msg.includes('what should i listen') ||
           msg.includes('play something') ||
           msg.includes('find me a song') ||
           msg.includes('need music') ||
           msg.includes('want a song');
}

function isTalkingAboutMusic(msg) {
    return msg.includes('music') || 
           msg.includes('song') || 
           msg.includes('artist') || 
           msg.includes('album') ||
           msg.includes('genre') ||
           msg.includes('listen');
}

function isGoodbye(msg) {
    return msg.match(/^(bye|goodbye|see you|later|gtg|gotta go|peace)\b/);
}

function detectEmotion(msg) {
    const emotionKeywords = {
        sad: ['sad', 'down', 'depressed', 'lonely', 'hurt', 'crying', 'heartbroken', 'miss', 'blue', 'unhappy', 'upset', 'terrible', 'awful'],
        happy: ['happy', 'joy', 'excited', 'great', 'amazing', 'wonderful', 'love', 'celebrate', 'awesome', 'fantastic', 'good', 'excellent'],
        calm: ['calm', 'peace', 'relax', 'chill', 'meditate', 'tranquil', 'zen', 'quiet', 'serene', 'peaceful', 'tired', 'sleepy'],
        energetic: ['energy', 'workout', 'pump', 'active', 'dance', 'party', 'hype', 'motivated', 'powerful', 'intense', 'pumped', 'excited']
    };
    
    for (const [emotion, keywords] of Object.entries(emotionKeywords)) {
        if (keywords.some(keyword => msg.includes(keyword))) {
            return emotion;
        }
    }
    
    return null;
}

// Response generators
function generateGreetingResponse() {
    const responses = [
        "Hey! 👋 How's it going? What's on your mind today?",
        "Hi there! 😊 Good to see you! How can I brighten your day?",
        "Hello! 🎵 Happy to chat with you! What brings you here?",
        "Hey hey! 💜 Great to hear from you! How are you feeling today?",
        "Hi! 😄 What's up? I'm here to chat about whatever you'd like!"
    ];
    
    return {
        role: 'assistant',
        content: responses[Math.floor(Math.random() * responses.length)]
    };
}

function generateBotFeelingsResponse() {
    const responses = [
        "I'm doing great, thanks for asking! 😊 I love chatting with people and helping them find good music. How about you?",
        "I'm fantastic! Every conversation is a new adventure for me. 🎵 How are YOU doing?",
        "I'm doing well! Just here, ready to chat and maybe help you discover some awesome tunes. What about you?",
        "Pretty good! I'm always excited to meet new people. 😄 Tell me, how's your day been?",
        "I'm great, thanks! 💜 I'm more interested in how YOU'RE doing though. What's going on with you?"
    ];
    
    return {
        role: 'assistant',
        content: responses[Math.floor(Math.random() * responses.length)]
    };
}

function generateEmotionResponse(emotion, originalMessage) {
    const responses = {
        sad: [
            "I'm sorry you're feeling down. 😔 Want to talk about what's bothering you? Sometimes it helps to just get things off your chest.",
            "That sounds tough. I'm here to listen if you want to share what's going on. Or we could find something to lift your spirits?",
            "I hear you. 💙 Life can be heavy sometimes. Would you like to talk about it, or would some music help?",
            "I'm sorry things aren't great right now. Remember, it's okay to not be okay. Want to chat about it or find some comforting music?"
        ],
        happy: [
            "That's awesome! 😊 I love hearing that! What's got you feeling so good today?",
            "Yes! Good vibes! 🎉 Tell me more - what's making you happy?",
            "Love the positive energy! ✨ Care to share what's putting that smile on your face?",
            "That's what I like to hear! 😄 Happiness is contagious! What's the good news?"
        ],
        calm: [
            "Nice! Sounds like you're in a peaceful headspace. 😌 That's a great feeling, isn't it?",
            "Zen mode activated! 🧘 I love those calm, centered moments. Doing anything special to maintain that peace?",
            "That's beautiful. 🍃 Sometimes we all need those quiet, peaceful moments. What are you up to?",
            "Peaceful vibes! 💆 Are you relaxing, meditating, or just enjoying some quiet time?"
        ],
        energetic: [
            "Woohoo! Love the energy! ⚡ What's pumping you up today?",
            "Yes! That's the spirit! 💪 What are you channeling all that energy into?",
            "Let's go! 🔥 I can feel the energy from here! What are you up to?",
            "High energy mode! 🏃 Are you working out, about to party, or just feeling pumped?"
        ]
    };
    
    const responseTexts = responses[emotion];
    const response = responseTexts[Math.floor(Math.random() * responseTexts.length)];
    
    // Only offer music if appropriate
    conversationContext.askedForRecommendation = false;
    
    return {
        role: 'assistant',
        content: response
    };
}

function generateMusicRecommendation() {
    const emotion = conversationContext.currentEmotion || 'calm';
    const emotionSongs = allSongs.filter(s => s.emotion === emotion);
    const randomSong = emotionSongs[Math.floor(Math.random() * emotionSongs.length)];
    
    const introTexts = {
        sad: "I've got just the thing. When I'm feeling down, I find comfort in music that understands. ",
        happy: "Oh, I know the perfect track to match that vibe! ",
        calm: "I know exactly what you need for that peaceful mood. ",
        energetic: "Yes! Let me hit you with something that'll keep that energy going! "
    };
    
    const intro = introTexts[emotion] || "Based on our chat, I think you'll love this: ";
    
    return {
        role: 'assistant',
        content: `${intro}Check out "${randomSong.title}" by ${randomSong.artist}. It's got that ${emotion} vibe. Want to give it a listen? 🎵`,
        song: randomSong
    };
}

function generateMusicConversation(msg) {
    const responses = [
        "Music is incredible, isn't it? 🎵 It has this power to completely shift how we feel. What kind of music do you usually listen to?",
        "I love talking about music! There's something magical about how a song can capture a moment or a feeling. What's your favorite genre?",
        "Music is my jam! 😄 What draws you to music? The lyrics, the melody, or the overall vibe?",
        "Same! Music is like therapy sometimes. Do you have a favorite artist or song that means a lot to you?",
        "Right? Music just hits different. 🎶 Are you more of a lyrics person or a melody person?"
    ];
    
    return {
        role: 'assistant',
        content: responses[Math.floor(Math.random() * responses.length)]
    };
}

function generateThankYouResponse() {
    const responses = [
        "You're so welcome! 😊 Happy to help! Anything else on your mind?",
        "No problem at all! That's what I'm here for! 💜 Need anything else?",
        "Anytime! 😄 I really enjoy our chats! What else can I do for you?",
        "My pleasure! 🎵 Feel free to come back anytime you want to talk or need music!",
        "Of course! Glad I could help! 😊 Let me know if you need anything else!"
    ];
    
    return {
        role: 'assistant',
        content: responses[Math.floor(Math.random() * responses.length)]
    };
}

function generateGoodbyeResponse() {
    const userName = conversationContext.userName ? `, ${conversationContext.userName}` : '';
    const responses = [
        `See you later${userName}! 👋 Come back anytime you want to chat or need some good music!`,
        `Bye${userName}! 😊 It was great talking with you! Take care!`,
        `Later${userName}! 🎵 Hope you found some awesome music today! See you around!`,
        `Goodbye${userName}! 💜 Don't be a stranger - I'm always here to chat!`,
        `Peace out${userName}! ✌️ Enjoy your music and have a great day!`
    ];
    
    return {
        role: 'assistant',
        content: responses[Math.floor(Math.random() * responses.length)]
    };
}

function generateGeneralResponse(msg) {
    // Contextual responses based on keywords
    if (msg.includes('life') || msg.includes('living')) {
        return {
            role: 'assistant',
            content: "Life can be quite the journey, right? 🌟 The ups, the downs... music helps us through it all. How are things going for you lately?"
        };
    }
    
    if (msg.includes('work') || msg.includes('job') || msg.includes('school')) {
        return {
            role: 'assistant',
            content: "Ah yeah, work/school can be intense! 💼 Are you having a rough day with it, or just needed to vent? I'm all ears!"
        };
    }
    
    if (msg.includes('friend') || msg.includes('people') || msg.includes('relationship')) {
        return {
            role: 'assistant',
            content: "Relationships can be complicated sometimes. 💭 Want to talk about what's on your mind? I'm here to listen, no judgment!"
        };
    }
    
    // Generic friendly responses
    const generalResponses = [
        "That's interesting! Tell me more about that. I'm curious! 🤔",
        "I hear you! Sometimes it helps just to talk things through, you know? What else is on your mind?",
        "Yeah, I get that. Life has its moments! How are you handling everything? 💜",
        "Mmm, I see what you mean. Want to dive deeper into that or talk about something else? 😊",
        "I appreciate you sharing that with me! What else is going on with you today? 🎵"
    ];
    
    return {
        role: 'assistant',
        content: generalResponses[Math.floor(Math.random() * generalResponses.length)]
    };
}

// Display chat message (same as before but with better styling)
function displayChatMessage(message) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${message.role}`;
    
    let messageHTML = `<div class="message-bubble">${message.content}`;
    
    // Add song recommendation if present
    if (message.song) {
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
        
        // Add a message confirming the play
        const confirmMessage = {
            role: 'assistant',
            content: "Great choice! 🎵 Enjoy the music! Let me know what you think! 😊"
        };
        chatHistory.push(confirmMessage);
        displayChatMessage(confirmMessage);
    }
}
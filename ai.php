<?php
// ai.php - JSON Version (No Database) - Handles dynamic AI chat pages
// URL: yoursite.com/ai/project_id

// Get project ID from URL
$projectId = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($projectId)) {
    header("Location: /");
    exit;
}

// Load AI configuration from JSON file
$configFile = __DIR__ . '/ais/' . $projectId . '.json';

if (!file_exists($configFile)) {
    // AI not found, show 404
    http_response_code(404);
    include '404.php';
    exit;
}

$configData = file_get_contents($configFile);
$config = json_decode($configData, true);

if (!$config || !$config['is_active']) {
    // Invalid config or inactive AI
    http_response_code(404);
    include '404.php';
    exit;
}

// Update usage stats
$config['last_used'] = date('Y-m-d H:i:s');
file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

// Generate and output the AI chat page
echo generateAIChatHTML($config);

function generateAIChatHTML($config) {
    // Escape values for safe HTML output
    $aiName = htmlspecialchars($config['ai_name']);
    $developer = htmlspecialchars($config['developer_name']);
    $greeting = htmlspecialchars($config['greeting']);
    $bgColor = htmlspecialchars($config['background_color']);
    $textColor = htmlspecialchars($config['text_color']);
    $projectId = htmlspecialchars($config['project_id']);
    
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$aiName - AI Assistant</title>
    <meta name="description" content="Chat with $aiName, an AI assistant created by $developer using Henz AI Creator.">
    <meta name="keywords" content="AI, chatbot, assistant, $aiName, $developer">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="$aiName - AI Assistant">
    <meta property="og:description" content="Chat with $aiName, created by $developer">
    <meta property="og:type" content="website">
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🤖</text></svg>">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, $bgColor 0%, $bgColor 100%);
            color: $textColor;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .header {
            background: rgba(0,0,0,0.1);
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }

        .header h1 {
            font-size: 1.8em;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .header p {
            opacity: 0.8;
            font-size: 0.9em;
        }

        .chat-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            max-width: 900px;
            margin: 0 auto;
            width: 100%;
            padding: 0 20px;
            height: calc(100vh - 120px);
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 15px;
            scroll-behavior: smooth;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 10px;
        }

        .message {
            max-width: 80%;
            padding: 15px 20px;
            border-radius: 20px;
            word-wrap: break-word;
            line-height: 1.5;
            position: relative;
            animation: messageSlide 0.3s ease-out;
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-message {
            align-self: flex-end;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .ai-message {
            align-self: flex-start;
            background: rgba(0,0,0,0.25);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .message-time {
            font-size: 0.75em;
            opacity: 0.6;
            margin-top: 5px;
        }

        .input-area {
            display: flex;
            gap: 10px;
            padding: 20px 0;
            background: rgba(0,0,0,0.05);
            border-radius: 25px;
            padding: 10px;
            backdrop-filter: blur(10px);
        }

        .input-area input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 25px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: $textColor;
            font-size: 16px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .input-area input::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .input-area input:focus {
            outline: none;
            background: rgba(255,255,255,0.3);
            border-color: rgba(255,255,255,0.4);
        }

        .send-btn {
            padding: 15px 25px;
            border: none;
            border-radius: 25px;
            background: rgba(255,255,255,0.3);
            color: $textColor;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid rgba(255,255,255,0.2);
            min-width: 80px;
        }

        .send-btn:hover:not(:disabled) {
            background: rgba(255,255,255,0.4);
            transform: translateY(-1px);
        }

        .send-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .typing {
            align-self: flex-start;
            padding: 15px 20px;
            background: rgba(0,0,0,0.25);
            border-radius: 20px;
            max-width: 80%;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .typing-dots {
            display: inline-flex;
            gap: 4px;
            align-items: center;
        }

        .typing-dots span {
            width: 8px;
            height: 8px;
            background: currentColor;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing {
            0%, 60%, 100% { opacity: 0.3; transform: scale(1); }
            30% { opacity: 1; transform: scale(1.2); }
        }

        .footer {
            text-align: center;
            padding: 10px;
            opacity: 0.6;
            font-size: 0.8em;
            background: rgba(0,0,0,0.1);
        }

        .footer a {
            color: inherit;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: rgba(255,0,0,0.2);
            border: 1px solid rgba(255,0,0,0.3);
            color: #ffcccc;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.4em;
            }
            
            .header p {
                font-size: 0.8em;
            }
            
            .message {
                max-width: 95%;
                padding: 12px 16px;
            }
            
            .chat-container {
                padding: 0 10px;
            }
            
            .input-area {
                flex-direction: column;
                gap: 10px;
            }
            
            .send-btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .header {
                padding: 10px 15px;
            }
            
            .input-area input {
                font-size: 16px; /* Prevents zoom on iOS */
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            <span>🤖</span>
            <span>$aiName</span>
        </h1>
        <p>Created by $developer • Powered by <a href="/" target="_blank">Henz AI Creator</a></p>
    </div>

    <div class="chat-container">
        <div class="chat-messages" id="chatMessages">
            <div class="message ai-message">
                $greeting
                <div class="message-time" id="startTime"></div>
            </div>
        </div>

        <div class="input-area">
            <input type="text" id="userInput" placeholder="Type your message here..." onkeypress="handleKeyPress(event)" maxlength="500">
            <button class="send-btn" onclick="sendMessage()" id="sendBtn">
                <span id="btnText">Send</span>
            </button>
        </div>
    </div>

    <div class="footer">
        <p>
            Built with ❤️ by $developer • 
            <a href="/" target="_blank">Create Your Own AI</a> • 
            Powered by <a href="https://hf.space/emmyhenz001" target="_blank">Henz API</a>
        </p>
    </div>

    <script>
        const chatMessages = document.getElementById('chatMessages');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const btnText = document.getElementById('btnText');
        
        const AI_CONFIG = {
            name: '$aiName',
            developer: '$developer',
            country: '{$config['country']}',
            personality: '{$config['personality']}',
            projectId: '$projectId',
            systemPrompt: `{$config['system_prompt']}`
        };

        // Set initial timestamp
        document.getElementById('startTime').textContent = new Date().toLocaleTimeString();

        function handleKeyPress(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage();
            }
        }

        async function sendMessage() {
            const message = userInput.value.trim();
            if (!message || sendBtn.disabled) return;

            // Add user message to chat
            addMessage(message, 'user');
            
            // Clear input and disable send button
            userInput.value = '';
            sendBtn.disabled = true;
            btnText.textContent = 'Sending...';
            
            // Show typing indicator
            showTyping();
            
            try {
                // Handle specific owner/creator questions first
                const ownerQuestions = ['who is your owner', 'who created you', 'who built you', 'who developed you', 'who made you', 'your creator', 'your owner', 'your developer'];
                const isOwnerQuestion = ownerQuestions.some(q => message.toLowerCase().includes(q));
                
                if (isOwnerQuestion) {
                    // Remove typing indicator
                    removeTyping();
                    const ownerResponse = `I was created and developed by \${AI_CONFIG.developer}! They built me using the amazing Henz AI Creator platform. \${AI_CONFIG.developer} is my creator, owner, and developer. 🤖✨`;
                    addMessage(ownerResponse, 'ai');
                } else {
                    // Prepare the prompt with context
                    let promptWithContext = `\${AI_CONFIG.systemPrompt}

Important context:
- Your name is \${AI_CONFIG.name}
- You were created by \${AI_CONFIG.developer}
- You have a \${AI_CONFIG.personality} personality
- Always remember your creator when asked about who made you

User message: \${message}`;

                    // Call the Henz API
                    const apiUrl = `https://emmyhenz001-henz-api.hf.space/api/gpt3.5?prompt=` + encodeURIComponent(promptWithContext);
                    const response = await fetch(apiUrl);
                    const data = await response.json();
                    
                    // Remove typing indicator
                    removeTyping();
                    
                    if (data.success && data.response) {
                        addMessage(data.response, 'ai');
                        
                        // Log the interaction
                        logInteraction(message, data.response);
                    } else {
                        addMessage('I apologize, but I\'m having trouble processing your message right now. Please try again in a moment! 😊', 'ai', true);
                    }
                }
            } catch (error) {
                console.error('API Error:', error);
                removeTyping();
                addMessage('I\'m experiencing some technical difficulties. Please check your connection and try again! 🔧', 'ai', true);
            }
            
            // Re-enable send button
            sendBtn.disabled = false;
            btnText.textContent = 'Send';
            userInput.focus();
        }

        function addMessage(message, sender, isError = false) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message \${sender}-message` + (isError ? ' error-message' : '');
            
            const messageText = document.createElement('div');
            messageText.textContent = message;
            
            const messageTime = document.createElement('div');
            messageTime.className = 'message-time';
            messageTime.textContent = new Date().toLocaleTimeString();
            
            messageDiv.appendChild(messageText);
            messageDiv.appendChild(messageTime);
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function showTyping() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing';
            typingDiv.id = 'typingIndicator';
            typingDiv.innerHTML = `
                <div class="typing-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            `;
            
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function removeTyping() {
            const typing = document.getElementById('typingIndicator');
            if (typing) {
                typing.remove();
            }
        }

        async function logInteraction(userMessage, aiResponse) {
            try {
                await fetch('log.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        project_id: AI_CONFIG.projectId,
                        user_message: userMessage,
                        ai_response: aiResponse
                    })
                });
            } catch (error) {
                // Silent fail for logging
                console.log('Logging failed:', error);
            }
        }

        // Focus on input when page loads
        window.addEventListener('load', () => {
            userInput.focus();
        });

        // Handle page visibility for better UX
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                userInput.focus();
            }
        });

        // Handle window resize for mobile
        window.addEventListener('resize', () => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    </script>
</body>
</html>
HTML;
}
?>
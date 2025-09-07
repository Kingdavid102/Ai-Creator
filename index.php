<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Henz AI Creator - Build Your Custom AI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h1 {
            color: #333;
            font-size: 3em;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header p {
            color: #666;
            font-size: 1.2em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 1.1em;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .color-input {
            height: 60px !important;
            cursor: pointer;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.2em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .loading {
            display: none;
            text-align: center;
            margin-top: 20px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #e0e0e0;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .result {
            display: none;
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-top: 30px;
        }

        .result h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
        }

        .result-link {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 1.1em;
        }

        .copy-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .preview {
            background: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-top: 10px;
            text-align: center;
        }

        .preview h4 {
            color: #333;
            margin-bottom: 10px;
        }

        .ai-preview {
            width: 100%;
            height: 200px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5em;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤖 Henz AI Creator</h1>
            <p>Create your own custom AI chatbot in minutes - no coding required!</p>
        </div>

        <form id="aiCreatorForm">
            <div class="form-group">
                <label for="aiName">🤖 AI Name</label>
                <input type="text" id="aiName" name="aiName" placeholder="Enter your AI's name (e.g., Alex Assistant)" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="developerName">👨‍💻 Your Name (Developer)</label>
                    <input type="text" id="developerName" name="developerName" placeholder="Your name" required>
                </div>
                <div class="form-group">
                    <label for="country">🌍 Country</label>
                    <select id="country" name="country" required>
                        <option value="">Select Country</option>
                        <option value="United States">United States</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Canada">Canada</option>
                        <option value="Australia">Australia</option>
                        <option value="Germany">Germany</option>
                        <option value="France">France</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="South Africa">South Africa</option>
                        <option value="India">India</option>
                        <option value="Brazil">Brazil</option>
                        <option value="Japan">Japan</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="backgroundColor">🎨 Background Color</label>
                    <input type="color" id="backgroundColor" name="backgroundColor" value="#667eea" class="color-input">
                </div>
                <div class="form-group">
                    <label for="textColor">✏️ Text Color</label>
                    <input type="color" id="textColor" name="textColor" value="#ffffff" class="color-input">
                </div>
            </div>

            <div class="form-group">
                <label for="personality">🎭 AI Personality</label>
                <select id="personality" name="personality" required>
                    <option value="friendly">😊 Friendly & Helpful</option>
                    <option value="professional">💼 Professional & Formal</option>
                    <option value="casual">😎 Casual & Relaxed</option>
                    <option value="creative">🎨 Creative & Artistic</option>
                    <option value="technical">🔧 Technical & Precise</option>
                    <option value="humorous">😄 Humorous & Fun</option>
                </select>
            </div>

            <div class="form-group">
                <label for="greeting">💬 Custom Greeting Message</label>
                <textarea id="greeting" name="greeting" rows="3" placeholder="Hi! I'm your AI assistant. How can I help you today?"></textarea>
            </div>

            <div class="preview">
                <h4>Preview Your AI:</h4>
                <div id="aiPreview" class="ai-preview" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <span id="previewName">Your AI</span>
                </div>
            </div>

            <button type="submit" class="btn" id="createBtn">
                🚀 Create My AI
            </button>
        </form>

        <div class="loading" id="loading">
            <div class="loading-spinner"></div>
            <h3>🔨 Establishing Your Link...</h3>
            <p>Publishing Your AI...</p>
        </div>

        <div class="result" id="result">
            <h3>🎉 Your AI Has Been Created Successfully!</h3>
            <p>Your custom AI is now live and ready to chat!</p>
            <div class="result-link" id="resultLink"></div>
            <button class="copy-btn" onclick="copyLink()">📋 Copy Link</button>
            <br><br>
            <button class="copy-btn" onclick="createAnother()">➕ Create Another AI</button>
        </div>
    </div>

    <script>
        // Update preview as user types
        document.getElementById('aiName').addEventListener('input', function() {
            document.getElementById('previewName').textContent = this.value || 'Your AI';
        });

        document.getElementById('backgroundColor').addEventListener('input', function() {
            const bg = this.value;
            const textColor = document.getElementById('textColor').value;
            document.getElementById('aiPreview').style.background = bg;
            document.getElementById('aiPreview').style.color = textColor;
        });

        document.getElementById('textColor').addEventListener('input', function() {
            const textColor = this.value;
            document.getElementById('aiPreview').style.color = textColor;
        });

        // Handle form submission
        document.getElementById('aiCreatorForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Show loading
            document.getElementById('createBtn').disabled = true;
            document.getElementById('loading').style.display = 'block';
            
            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                // Send data to PHP backend
                const response = await fetch('create_ai.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                // Hide loading
                document.getElementById('loading').style.display = 'none';
                
                if (result.success) {
                    // Show success result
                    document.getElementById('result').style.display = 'block';
                    document.getElementById('resultLink').textContent = result.ai_link;
                    
                    // Scroll to result
                    document.getElementById('result').scrollIntoView({ behavior: 'smooth' });
                } else {
                    // Show error
                    alert('Error creating AI: ' + (result.error || 'Unknown error'));
                    document.getElementById('createBtn').disabled = false;
                }
                
            } catch (error) {
                // Hide loading and show error
                document.getElementById('loading').style.display = 'none';
                alert('Network error. Please check your connection and try again. Error: ' + error.message);
                document.getElementById('createBtn').disabled = false;
                console.error('Full error:', error);
            }
        });

        function copyLink() {
            const link = document.getElementById('resultLink').textContent;
            navigator.clipboard.writeText(link).then(() => {
                const btn = event.target;
                const originalText = btn.textContent;
                btn.textContent = '✅ Copied!';
                setTimeout(() => {
                    btn.textContent = originalText;
                }, 2000);
            });
        }

        function createAnother() {
            document.getElementById('result').style.display = 'none';
            document.getElementById('aiCreatorForm').reset();
            document.getElementById('createBtn').disabled = false;
            document.getElementById('previewName').textContent = 'Your AI';
            document.getElementById('aiPreview').style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            document.getElementById('aiPreview').style.color = '#ffffff';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
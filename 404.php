<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Not Found - Henz AI Creator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
        }

        .error-container {
            max-width: 600px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .error-icon {
            font-size: 6em;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 3em;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .error-message {
            font-size: 1.2em;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.5;
        }

        .error-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: rgba(255, 255, 255, 0.9);
            color: #667eea;
        }

        .btn-primary:hover {
            background: white;
            color: #764ba2;
        }

        @media (max-width: 768px) {
            .error-title {
                font-size: 2em;
            }
            
            .error-message {
                font-size: 1em;
            }
            
            .error-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🤖❌</div>
        <h1 class="error-title">AI Not Found</h1>
        <p class="error-message">
            Sorry, the AI you're looking for doesn't exist or may have been removed.
            <br><br>
            This could happen if:
            <br>• The AI link was typed incorrectly
            <br>• The AI was deleted by its creator
            <br>• The AI ID has expired
        </p>
        
        <div class="error-actions">
            <a href="/" class="btn btn-primary">🚀 Create Your Own AI</a>
            <a href="javascript:history.back()" class="btn">← Go Back</a>
        </div>
    </div>
</body>
</html>
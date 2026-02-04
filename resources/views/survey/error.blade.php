<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Error' }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #f56565 0%, #ed8936 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .container { 
            max-width: 600px; 
            background: white; 
            padding: 50px; 
            border-radius: 20px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
        }
        .error-icon { 
            font-size: 80px; 
            color: #e53e3e; 
            margin-bottom: 20px;
        }
        h1 { color: #2d3748; margin-bottom: 10px; }
        p { color: #718096; line-height: 1.6; }
        .home-link {
            display: inline-block;
            background: #4f46e5;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 50px;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">❌</div>
        <h1>{{ $title ?? 'Error' }}</h1>
        <p>{{ $message ?? 'An error occurred while accessing the survey.' }}</p>
        <a href="{{ url('/') }}" class="home-link">Return to Homepage</a>
    </div>
</body>
</html>
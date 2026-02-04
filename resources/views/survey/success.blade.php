<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Submitted Successfully</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        .success-icon { 
            font-size: 80px; 
            color: #10b981; 
            margin-bottom: 20px;
        }
        h1 { color: #2d3748; margin-bottom: 10px; }
        p { color: #718096; line-height: 1.6; }
        .certificate-note {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
            text-align: left;
        }
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
        <div class="success-icon">✅</div>
        <h1>{{ $message ?? 'Survey Submitted Successfully!' }}</h1>
        <p>{{ $subtitle ?? 'Thank you for your valuable feedback.' }}</p>
        
        <div class="certificate-note">
            <h3>🎉 Your Certificate</h3>
            <p>Your certificate of participation has been generated and will be emailed to you shortly.</p>
            <p>Please check your email inbox (and spam folder) for the certificate.</p>
        </div>
        
        <p>You can now close this window or return to the homepage.</p>
        <a href="{{ url('/') }}" class="home-link">Return to Homepage</a>
    </div>
</body>
</html>
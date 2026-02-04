<!DOCTYPE html>
<html>
<head>
    <title>Survey Invitation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { padding: 30px 20px; }
        .button { 
            display: inline-block; 
            background: #4f46e5; 
            color: white; 
            padding: 12px 30px; 
            text-decoration: none; 
            border-radius: 5px; 
            margin: 20px 0; 
            font-weight: bold;
        }
        .footer { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #eee; 
            text-align: center; 
            color: #666; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Survey Invitation</h1>
        </div>
        
        <div class="content">
            <h2>Hello {{ $participantName }},</h2>
            
            <p>Thank you for attending <strong>{{ $eventName }}</strong> on {{ $eventDate }}.</p>
            
            <p>We value your feedback! Please take a few minutes to complete our survey:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $surveyUrl }}" class="button">
                    Take Survey Now
                </a>
            </div>
            
            <p>Or copy this link:</p>
            <div style="background: #f3f4f6; padding: 10px; border-radius: 5px; margin: 10px 0;">
                <code style="word-break: break-all;">{{ $surveyUrl }}</code>
            </div>
            
            <p><strong>Note:</strong> This survey link expires in 7 days.</p>
        </div>
        
        <div class="footer">
            <p>Best regards,<br>
            <strong>{{ config('app.name', 'Event System') }} Team</strong></p>
        </div>
    </div>
</body>
</html>
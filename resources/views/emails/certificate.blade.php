<!DOCTYPE html>
<html>
<head>
    <title>Your Certificate</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #10b981; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { padding: 30px 20px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Congratulations!</h1>
        </div>
        
        <div class="content">
            <div style="text-align: center; margin: 20px 0; font-size: 48px;">🏆</div>
            
            <h2>Hello {{ $participantName }},</h2>
            
            <p>Thank you for completing the survey for <strong>{{ $eventName }}</strong> held on {{ $eventDate }}.</p>
            
            <p>We're pleased to attach your <strong>Certificate of Participation</strong>.</p>
            
            <div style="background: #f0f9ff; border-left: 4px solid #0ea5e9; padding: 15px; margin: 20px 0;">
                <p><strong>Certificate Details:</strong></p>
                <p>📅 Event: {{ $eventName }}<br>
                📅 Date: {{ $eventDate }}</p>
            </div>
            
            <p>The certificate PDF is attached to this email.</p>
            
            <p>Thank you for your participation!</p>
        </div>
        
        <div class="footer">
            <p>Best regards,<br>
            <strong>{{ config('app.name', 'Event System') }} Team</strong></p>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { 
            font-family: 'DejaVu Sans', 'Arial', sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .certificate { 
            background: white; 
            border: 20px solid #0ea5e9; 
            padding: 50px; 
            text-align: center;
            width: 210mm; /* A4 width */
            height: 297mm; /* A4 height */
            position: relative;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header { color: #1e40af; margin-bottom: 40px; }
        .title { 
            font-size: 48px; 
            font-weight: bold; 
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .subtitle { font-size: 24px; color: #4b5563; margin-bottom: 20px; }
        .content { margin: 60px 0; padding: 0 50px; }
        .name { 
            font-size: 42px; 
            color: #1e293b; 
            margin: 30px 0; 
            font-weight: bold;
        }
        .event { 
            font-size: 28px; 
            color: #475569; 
            margin: 20px 0;
        }
        .date { 
            font-size: 20px; 
            color: #64748b; 
            margin: 10px 0;
        }
        .cert-number { 
            position: absolute; 
            bottom: 30px; 
            right: 30px; 
            font-size: 14px; 
            color: #94a3b8;
        }
        .signature { 
            margin-top: 100px;
            display: flex;
            justify-content: space-between;
            padding: 0 50px;
        }
        .signature-line {
            width: 200px;
            height: 1px;
            background: #000;
            margin: 0 auto 10px;
        }
        .border-decoration {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        .decoration-corner {
            position: absolute;
            width: 80px;
            height: 80px;
            border: 2px solid #0ea5e9;
        }
        .top-left { top: 20px; left: 20px; border-right: none; border-bottom: none; }
        .top-right { top: 20px; right: 20px; border-left: none; border-bottom: none; }
        .bottom-left { bottom: 20px; left: 20px; border-right: none; border-top: none; }
        .bottom-right { bottom: 20px; right: 20px; border-left: none; border-top: none; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="border-decoration">
            <div class="decoration-corner top-left"></div>
            <div class="decoration-corner top-right"></div>
            <div class="decoration-corner bottom-left"></div>
            <div class="decoration-corner bottom-right"></div>
        </div>
        
        <div class="header">
            <div class="title">CERTIFICATE OF PARTICIPATION</div>
            <div class="subtitle">This certificate is proudly presented to</div>
        </div>
        
        <div class="content">
            <div class="name">{{ $participant->user->name }}</div>
            <div class="subtitle">for successfully participating in</div>
            <div class="event">{{ $event->name }}</div>
            <div class="date">on {{ $event->date->format('F j, Y') }}</div>
            
            <div style="margin: 40px 0; font-size: 18px; color: #6b7280;">
                <p>In recognition of valuable contribution and active participation<br>
                in the event proceedings and survey completion.</p>
            </div>
        </div>
        
        <div class="signature">
            <div>
                <div class="signature-line"></div>
                <div style="margin-top: 10px;">Event Director</div>
                <div style="color: #6b7280; font-size: 14px;">{{ config('app.name', 'Event System') }}</div>
            </div>
            
            <div>
                <div style="margin-top: 40px;">Date: {{ $issuedDate }}</div>
                <div style="color: #6b7280; font-size: 14px; margin-top: 5px;">Issued Electronically</div>
            </div>
        </div>
        
        <div class="cert-number">
            Certificate No: {{ $certificateNumber }}
        </div>
    </div>
</body>
</html>
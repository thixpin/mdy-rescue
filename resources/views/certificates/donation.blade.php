<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Donation Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px;
            background: #fff;
        }
        .certificate {
            border: 2px solid #000;
            padding: 40px;
            text-align: center;
        }
        .header {
            margin-bottom: 40px;
        }
        .title {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .subtitle {
            font-size: 24px;
            margin-bottom: 40px;
        }
        .content {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 40px;
        }
        .footer {
            font-size: 16px;
            margin-top: 40px;
        }
        .verification-code {
            font-family: monospace;
            font-size: 14px;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="header">
            <div class="title">Certificate of Donation</div>
            <div class="subtitle">This is to certify that</div>
        </div>
        
        <div class="content">
            <p><strong>{{ $donation->name }}</strong></p>
            <p>has made a generous donation of</p>
            <p><strong>{{ $donation->amount_in_text }}</strong></p>
            <p>on {{ $donation->donate_date->format('F d, Y') }}</p>
            
            @if($donation->description)
                <p>{{ $donation->description }}</p>
            @endif
        </div>
        
        <div class="footer">
            <p>This certificate is issued by Mandalay Rescue</p>
            <p>Certificate ID: {{ $donation->short_id }}</p>
            <div class="verification-code">
                Verify at: {{ url('/verify-certificate?id=' . $donation->short_id) }}
            </div>
        </div>
    </div>
</body>
</html> 
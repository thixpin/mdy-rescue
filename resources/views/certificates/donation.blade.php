<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Donation Certificate</title>
    <style>
        @font-face {
            font-family: 'Pyidaungsu';
            src: url("{!! public_path('assets/fonts/Pyidaungsu-Regular.ttf') !!}") format("truetype");
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Pyidaungsu';
            src: url("{!! public_path('assets/fonts/Pyidaungsu-Bold.ttf') !!}") format("truetype");
            font-weight: bold;
            font-style: normal;
        }
        body {
            font-family: 'Pyidaungsu', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            /* set size for a4 */
            width: 210mm;
            height: 296mm;
        }

        .input-txt {
            color: #5325c3;
            font-weight: 600;
            left: 20mm;
            right: 20mm;
        }
        .certificate {
            width: 210mm; /* A4 width */
            height: 296mm; /* A4 height */
            margin: 0;
            padding: 0;
            position: relative;
            background-image: url("{!! storage_path('app/private/certificate-bg.jpg') !!}");
            /* background-image: url("{!! asset('assets/certificates/certificate-bg.jpg') !!}"); */
            background-size: 210mm 296mm;
            background-position: top center;
            background-repeat: no-repeat;
        }
        .content {
            position: absolute;
            top: 112mm; /* Adjust this value to position content correctly */
            left: 10mm;
            right: 10mm;
            font-size: 16pt;
            line-height: 2;
            text-align: left;
            font-family: 'Pyidaungsu', Arial, sans-serif;
        }
        .content p {
            margin: 5mm 0;
            padding: 0;
            display: inline-block;
            text-align: center;
        }
        .date {
            position: absolute;
            top: -2mm;
            left: 131mm;
            right: 0mm;
            text-align: left;
        }
        .name {
            position: absolute;
            top: 8mm;
            font-weight: bold;
            font-size: 24pt;
        }

        .description {
            position: absolute;
            top: 23mm;
        }
        .amount {
            position: absolute;
            top: 71mm;
        }

        .amount-text {
            position: absolute;
            top: 83mm;
        }
        
        .verification-code {
            position: absolute;
            bottom: 68mm;
            left: 30mm;
            right: 30mm;
            text-align: center;
            font-size: 9pt;
            color: #666;
        }
        .verification-code a {
            color: #444;
            text-decoration: none;
        }
        @page {
            margin: 0;
            padding: 0;
            size: A4 portrait;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="content">
            <p class="date input-txt">{{ $donation->formated_date }} </p>
            <p class="name input-txt">{{ $donation->name }}</p>
            @if($donation->description)
                <p class="description input-txt">({{ $donation->description }})</p>
            @endif
            <p class="amount input-txt">{{ $donation->amount }}
            <p class="amount-text input-txt">{{ $donation->amount_in_text }} </p>
            
        </div>

        <div class="verification-code">
            <strong>Verify at: </strong> <a href="{{ url('/verify-certificate?id=' . $donation->short_id) }}">
                {{ url('/verify-certificate?id=' . $donation->short_id) }}
            </a>
        </div>
    </div>
</body>
</html> 
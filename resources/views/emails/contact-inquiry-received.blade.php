<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>New Luxury Inquiry Received</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }
        .header {
            background-color: #1A1A1A;
            padding: 28px 32px;
            border-bottom: 3px solid #FF6B35;
        }
        .brand {
            color: #ffffff;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .brand span {
            color: #FF6B35;
        }
        .tagline {
            color: #94a3b8;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 4px;
        }
        .content {
            padding: 32px;
        }
        .badge {
            display: inline-block;
            background-color: rgba(255, 107, 53, 0.1);
            color: #FF6B35;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 9999px;
            margin-bottom: 16px;
        }
        h2 {
            font-size: 22px;
            color: #0f172a;
            margin: 0 0 12px 0;
            font-weight: 800;
        }
        p {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin: 0 0 20px 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
        }
        .details-table td {
            padding: 12px 16px;
            font-size: 13px;
            border-bottom: 1px solid #e2e8f0;
        }
        .details-table td.label {
            width: 32%;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        .details-table td.value {
            color: #0f172a;
            font-weight: 600;
        }
        .message-box {
            background-color: #f1f5f9;
            border-left: 3px solid #FF6B35;
            padding: 16px;
            border-radius: 0 8px 8px 0;
            font-size: 13px;
            line-height: 1.6;
            color: #334155;
            margin: 20px 0;
        }
        .action-button {
            display: inline-block;
            background-color: #FF6B35;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 12px 24px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 32px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <div class="brand">TISHA <span>Real Estate</span></div>
            <div class="tagline">Confidential Client Advisory</div>
        </div>
        <div class="content">
            <span class="badge">New Client Lead</span>
            <h2>Direct Website Inquiry</h2>
            <p>A new concierge inquiry has been submitted through the TISHA Real Estate frontend.</p>

            <table class="details-table">
                <tr>
                    <td class="label">Full Name</td>
                    <td class="value">{{ $inquiry->name }}</td>
                </tr>
                <tr>
                    <td class="label">Email Address</td>
                    <td class="value"><a href="mailto:{{ $inquiry->email }}" style="color: #FF6B35;">{{ $inquiry->email }}</a></td>
                </tr>
                <tr>
                    <td class="label">Phone</td>
                    <td class="value"><a href="tel:{{ $inquiry->phone }}" style="color: #0f172a; text-decoration: none;">{{ $inquiry->phone ?: 'Not provided' }}</a></td>
                </tr>
                <tr>
                    <td class="label">Subject</td>
                    <td class="value">{{ $inquiry->subject ?: 'General Inquiry' }}</td>
                </tr>
                <tr>
                    <td class="label">Received At</td>
                    <td class="value">{{ $inquiry->created_at?->format('F d, Y \a\t H:i T') ?? now()->toDayDateTimeString() }}</td>
                </tr>
            </table>

            <div style="font-weight: 700; font-size: 12px; text-transform: uppercase; color: #64748b; margin-top: 16px;">
                Client Message:
            </div>
            <div class="message-box">
                {!! nl2br(e($inquiry->message)) !!}
            </div>

            <div style="text-align: center; margin-top: 28px;">
                <a href="{{ route('admin.inquiries') }}" class="action-button">
                    View In Admin Console
                </a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} TISHA Real Estate. Automated Brokerage Notification.
        </div>
    </div>
</body>
</html>

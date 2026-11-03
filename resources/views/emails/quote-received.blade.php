<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Quote Request</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f4f5f7; margin: 0; padding: 24px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden;">
        <div style="background: #1e3a66; padding: 24px; color: #fff;">
            <h1 style="margin: 0; font-size: 20px;">New Quote Request</h1>
            <p style="margin: 4px 0 0; opacity: 0.8; font-size: 14px;">
                Reference: <strong>{{ $quote->reference }}</strong>
            </p>
        </div>

        <div style="padding: 24px;">
            <table style="width: 100%; font-size: 14px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #5B6470; width: 140px;">Name</td>
                    <td style="padding: 8px 0;"><strong>{{ $quote->full_name }}</strong></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #5B6470;">Company</td>
                    <td style="padding: 8px 0;">{{ $quote->company ?: '—' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #5B6470;">Phone</td>
                    <td style="padding: 8px 0;"><a href="tel:{{ $quote->phone }}" style="color: #325a96;">{{ $quote->phone }}</a></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #5B6470;">Email</td>
                    <td style="padding: 8px 0;"><a href="mailto:{{ $quote->email }}" style="color: #325a96;">{{ $quote->email }}</a></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #5B6470;">Interested in</td>
                    <td style="padding: 8px 0;"><strong>{{ $quote->interest }}</strong></td>
                </tr>
            </table>

            @if ($quote->details)
                <div style="margin-top: 20px; padding: 16px; background: #f4f5f7; border-radius: 6px;">
                    <div style="font-size: 12px; color: #5B6470; margin-bottom: 6px;">DETAILS</div>
                    <div style="font-size: 14px; white-space: pre-line;">{{ $quote->details }}</div>
                </div>
            @endif

            <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e4e7ec; font-size: 12px; color: #5B6470;">
                Received {{ $quote->created_at->format('M j, Y \a\t H:i') }} from {{ $quote->ip_address }}
            </div>
        </div>
    </div>
</body>
</html>
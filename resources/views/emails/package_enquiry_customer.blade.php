<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>We received your enquiry</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">
          <tr>
            <td style="background:#005fcc;padding:20px 28px;">
              <span style="color:#ffffff;font-size:20px;font-weight:800;">Fare<span style="color:#f47b20;">Buzzer</span></span>
            </td>
          </tr>
          <tr>
            <td style="padding:28px;">
              <h2 style="margin:0 0 12px;font-size:18px;">Thank you, {{ $enquiry->name }}!</h2>
              <p style="font-size:14px;line-height:1.7;color:#444;">
                We've received your enquiry
                @if($enquiry->holidayPackage) for <strong>{{ $enquiry->holidayPackage->title }}</strong> @endif
                and our travel experts will get back to you shortly.
              </p>

              <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;margin-top:12px;border-collapse:collapse;">
                @if($enquiry->travel_date)
                  <tr><td style="color:#888;">Travel Date</td><td>{{ $enquiry->travel_date->format('d M Y') }}</td></tr>
                @endif
                @if($enquiry->travellers)
                  <tr><td style="color:#888;">Travellers</td><td>{{ $enquiry->travellers }}</td></tr>
                @endif
                <tr><td style="color:#888;">Phone</td><td>{{ $enquiry->phone }}</td></tr>
                <tr><td style="color:#888;">Email</td><td>{{ $enquiry->email }}</td></tr>
              </table>

              <p style="font-size:12px;color:#aaa;margin-top:24px;">This is an automated confirmation — no need to reply.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

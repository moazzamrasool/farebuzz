<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank you for your enquiry</title>
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
                We've received your enquiry for the <strong>{{ $landingPageName }}</strong>. A travel expert
                will call you during business hours to help plan your trip.
              </p>

              <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;margin-top:12px;border-collapse:collapse;">
                <tr><td style="color:#888;width:140px;">Phone</td><td>{{ $enquiry->phone }}</td></tr>
                <tr><td style="color:#888;">Details</td><td>{{ $enquiry->message ?? '—' }}</td></tr>
              </table>

              <p style="font-size:14px;line-height:1.7;color:#444;margin-top:20px;">
                Need us sooner? Call <a href="tel:+918447843676" style="color:#005fcc;">+91 84478 43676</a>
                or <a href="https://wa.me/918447843676" style="color:#1da66b;">message us on WhatsApp</a>.
              </p>

              <p style="font-size:12px;color:#aaa;margin-top:24px;">This is an automated confirmation — no need to reply.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

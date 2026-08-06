<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New package enquiry</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f5;font-family:Arial,Helvetica,sans-serif;color:#111;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f5f5f5;padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">
          <tr>
            <td style="background:#111827;padding:20px 28px;">
              <span style="color:#ffffff;font-size:16px;font-weight:700;">New Package Enquiry</span>
            </td>
          </tr>
          <tr>
            <td style="padding:28px;">
              <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;">
                <tr><td style="color:#888;width:140px;">Package</td><td>{{ $enquiry->holidayPackage->title ?? 'N/A' }}</td></tr>
                <tr><td style="color:#888;">Name</td><td>{{ $enquiry->name }}</td></tr>
                <tr><td style="color:#888;">Email</td><td>{{ $enquiry->email }}</td></tr>
                <tr><td style="color:#888;">Phone</td><td>{{ $enquiry->phone }}</td></tr>
                <tr><td style="color:#888;">Travel Date</td><td>{{ optional($enquiry->travel_date)->format('d M Y') ?? '—' }}</td></tr>
                <tr><td style="color:#888;">Travellers</td><td>{{ $enquiry->travellers ?? '—' }}</td></tr>
                <tr><td style="color:#888;vertical-align:top;">Message</td><td>{{ $enquiry->message ?? '—' }}</td></tr>
                <tr><td style="color:#888;">Submitted</td><td>{{ $enquiry->created_at->format('d M Y, h:i A') }}</td></tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

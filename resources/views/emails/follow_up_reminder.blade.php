<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Follow-up due</title>
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
              <h2 style="margin:0 0 12px;font-size:18px;">Follow-up due{{ $followUp->due_at->isPast() ? ' (overdue)' : '' }}</h2>
              <p style="font-size:14px;line-height:1.7;color:#444;">
                A follow-up with <strong>{{ $followUp->leadable->name }}</strong> was due on
                <strong>{{ $followUp->due_at->format('d M Y, h:i A') }}</strong>.
              </p>

              @if($followUp->note)
                <table role="presentation" width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;margin-top:12px;border-collapse:collapse;">
                  <tr><td style="color:#888;">Note</td><td>{{ $followUp->note }}</td></tr>
                  <tr><td style="color:#888;">Phone</td><td>{{ $followUp->leadable->phone }}</td></tr>
                  <tr><td style="color:#888;">Email</td><td>{{ $followUp->leadable->email }}</td></tr>
                </table>
              @endif

              <p style="font-size:12px;color:#aaa;margin-top:24px;">Open the lead in the CRM to mark this follow-up complete.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

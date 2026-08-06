<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $lead->name }}</title>
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
            <td style="padding:28px;font-size:14px;line-height:1.7;color:#444;">
              {!! $body !!}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

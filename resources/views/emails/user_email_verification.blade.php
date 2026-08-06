<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify your email</title>
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
              <h2 style="margin:0 0 12px;font-size:18px;">Hi {{ $user->name }},</h2>
              <p style="font-size:14px;line-height:1.7;color:#444;">
                Thanks for signing up! Please confirm your email address to activate your account.
              </p>

              <p style="text-align:center;margin:28px 0;">
                <a href="{{ route('user.verify-email', $user->email_verification_token) }}"
                   style="background:#005fcc;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:14px;font-weight:700;display:inline-block;">
                  Verify Email
                </a>
              </p>

              <p style="font-size:12px;color:#aaa;">
                If the button doesn't work, copy and paste this link into your browser:<br>
                {{ route('user.verify-email', $user->email_verification_token) }}
              </p>

              <p style="font-size:12px;color:#aaa;margin-top:24px;">
                If you didn't create this account, you can safely ignore this email.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

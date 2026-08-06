<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Quotation</title>
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
              <h2 style="margin:0 0 12px;font-size:18px;">Hi {{ $quotation->customer_name }},</h2>
              <p style="font-size:14px;line-height:1.7;color:#444;">
                Thanks for your interest
                @if($quotation->holidayPackage) in <strong>{{ $quotation->holidayPackage->title }}</strong> @endif
                — please find your quotation <strong>{{ $quotation->quotation_number }}</strong> attached as a PDF,
                totalling <strong>&#8377;{{ number_format($quotation->total_amount, 2) }}</strong>.
              </p>
              @if($quotation->valid_until)
                <p style="font-size:13px;color:#888;">This quotation is valid until {{ $quotation->valid_until->format('d M Y') }}.</p>
              @endif
              <p style="font-size:14px;line-height:1.7;color:#444;">
                Ready to book, or have questions? Just reply to this email and our travel experts will help you out.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>

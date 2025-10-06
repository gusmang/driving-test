<!DOCTYPE html>
<html lang="en" style="font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;">
  <body style="max-width: 600px; margin: auto; background: white; padding: 20px; border-radius: 8px;">

    <h2 style="text-align: center; color: #2d3748;">Verify Your Email Address</h2>

    <p>Hi {{ $user->first_name ?? 'User' }},</p>

    <p>Thank you for registering! Please verify your email address by clicking the button below:</p>

    <p style="text-align: center;">
      <a href="{{ $verificationUrl }}" 
         style="background: #38a169; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px;">
         Verify Email
      </a>
    </p>

    <p>If the button above doesn’t work, copy and paste this link into your browser:</p>
    <p style="word-break: break-all;">
      <a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a>
    </p>

    <!-- PIN Section untuk Student -->
    @if($user->role === 'student' && isset($pin))
    <div style="text-align: center; margin: 30px 0;">
      <p style="font-size: 18px; color: #2d3748;">Your Unique PIN:</p>
      <p style="font-size: 36px; font-weight: bold; color: #e53e3e; letter-spacing: 4px;">
        {{ $pin }}
      </p>
      <p style="font-size: 14px; color: #718096;">Use this PIN to login to your account.</p>
    </div>
    @endif

    <p>If you did not create an account, no further action is required.</p>

    <br>
    <p>Regards,<br><strong>Your Company Name</strong></p>

    <hr style="margin-top: 30px;">
    <p style="font-size: 12px; color: #718096; text-align: center;">
      This is an automated message. Please do not reply.
    </p>
  </body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your New PIN is Ready</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 500px; background-color: white; padding: 20px; margin: auto; border-radius: 8px;">
        <tr>
            <td style="text-align: center;">
                <h2 style="color: #333;">Your New PIN Has Been Generated</h2>
                <p>Hello <strong>{{ $user->first_name }}</strong>,</p>
                <p>Here is your new login PIN:</p>
                
                <div style="font-size: 24px; font-weight: bold; background-color: #f0f0f0; padding: 10px; border-radius: 4px; display: inline-block;">
                    {{ $pin }}
                </div>

                <p style="margin-top: 20px;">Please use this PIN to log back into your account.</p>
                <p><a href="{{ url('login') }}" style="display: inline-block; padding: 10px 20px; color: white; background-color: #007bff; text-decoration: none; border-radius: 4px;">Login Now</a></p>

                <p style="color: #888; font-size: 12px; margin-top: 20px;">If you did not request a new PIN, you can safely ignore this email.</p>
            </td>
        </tr>
    </table>
</body>
</html>
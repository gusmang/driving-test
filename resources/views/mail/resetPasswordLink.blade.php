<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Reset Password</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      padding: 20px;
      color: #333;
    }
    .container {
      background: #ffffff;
      padding: 30px;
      max-width: 500px;
      margin: auto;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    .btn {
      display: inline-block;
      padding: 12px 20px;
      background-color: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      margin-top: 20px;
    }
    .footer {
      margin-top: 30px;
      font-size: 12px;
      color: #888;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Password Reset Request</h2>
    <p>Hello {{$name}},</p>
    <p>We received a request to reset your password. Click the button below to set a new password:</p>

    <a href="{{$reset_link}}" class="btn">Reset Password</a>

    <p>If you did not request this, you can safely ignore this email.</p>

    <div class="footer">
      &copy; {{$app_name}} - All Rights Reserved
    </div>
  </div>

</body>
</html>
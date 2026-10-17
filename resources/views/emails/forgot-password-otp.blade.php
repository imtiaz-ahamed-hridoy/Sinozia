<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password OTP</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            color: #333333;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding: 40px 0;
        }

        .main-container {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 600px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .header {
            background-color: #4f46e5;
            padding: 32px 40px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .content {
            padding: 40px;
        }

        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 20px;
        }

        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 25px;
        }

        .otp-container {
            text-align: center;
            margin: 35px 0;
        }

        .otp-code {
            display: inline-block;
            background-color: #eef2ff;
            color: #4f46e5;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            padding: 16px 32px;
            border-radius: 8px;
            margin: 0;
            border: 2px dashed #c7d2fe;
        }

        .warning-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 25px;
            font-size: 14px;
            line-height: 1.5;
            color: #92400e;
        }

        .footer {
            padding: 30px 40px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .footer p {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #6b7280;
            line-height: 1.5;
        }

        .footer a {
            color: #4f46e5;
            text-decoration: underline;
        }

    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main-container" width="100%" cellpadding="0" cellspacing="0">
            <!-- Header -->
            <tr>
                <td class="header">
                    <h1>{{ config('app.name') }}</h1>
                </td>
            </tr>

            <!-- Content -->
            <tr>
                <td class="content">
                    <div class="greeting">Hello {{ $user->name }},</div>

                    <div class="message">
                        We received a request to reset the password for your account. To proceed, please use the following One-Time Password (OTP):
                    </div>

                    <div class="otp-container">
                        <div class="otp-code">{{ $code }}</div>
                    </div>

                    <div class="warning-box">
                        <strong>Security Notice:</strong> This OTP code is valid for 1 minute. Please do not share this code with anyone. Our team will never ask for your password or OTP.
                    </div>

                    <div class="message" style="margin-bottom: 0;">
                        If you did not request a password reset, you can safely ignore this email. If you need further assistance, please contact our support team.
                    </div>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p>Best regards,<br>
                        <strong>{{ config('app.name') }}</strong></p>
                    {{-- [Contact_Information]  --}}
                </td>
            </tr>
        </table>
    </center>

</body>
</html>

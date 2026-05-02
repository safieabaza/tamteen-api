<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:20px;">
        <tr>
            <td align="center">

                <!-- Main Container -->
                <table width="500" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:10px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#0d6efd; padding:20px; text-align:center; color:white;">
                            <h1 style="margin:0;">Tamteen</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:30px; text-align:center;">

                            <h2 style="margin-bottom:10px;">Verify Your Email</h2>

                            <p style="color:#555;">
                                Use the code below to complete your login
                            </p>

                            <!-- OTP BOX -->
                            <div style="
                                margin:20px auto;
                                padding:15px;
                                background:#f1f5ff;
                                border-radius:8px;
                                font-size:28px;
                                font-weight:bold;
                                letter-spacing:5px;
                                color:#0d6efd;
                                width:200px;
                            ">
                                {{ $code }}
                            </div>

                            <p style="color:#888;">
                                This code expires in 5 minutes
                            </p>

                            <!-- Button -->
                            <a href="#" style="
                                display:inline-block;
                                margin-top:20px;
                                padding:12px 25px;
                                background:#0d6efd;
                                color:#ffffff;
                                text-decoration:none;
                                border-radius:5px;
                                font-size:14px;
                            ">
                                Verify Now
                            </a>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9f9f9; padding:15px; text-align:center; font-size:12px; color:#999;">
                            © 2026 Tamteen. All rights reserved.
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>
</html>
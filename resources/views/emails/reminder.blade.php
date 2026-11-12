<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f4f4f4; font-family: Arial, sans-serif; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; }
        .device-info { background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .button { background-color: #0000bf; color: #ffffff !important; padding: 14px 30px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold; }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e1e1e1; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <tr>
                        <td align="left" style="background-color: #0000bf; padding: 25px 40px; color: #ffffff;">
                            <h1 style="margin: 0; font-size: 22px; font-weight: bold; letter-spacing: 0.5px;">Easy Inventory</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px; color: #333333; line-height: 1.6;">
                            <h2 style="margin-top: 0; color: #0000bf; font-size: 20px;">{{ $reminderTitle }}</h2>
                            <p>Hallo {{ $toName }},</p>
                            <p>{!! nl2br(e($reminderMessage)) !!}</p>

                            <div class="device-info">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding-bottom: 10px;">
                                            <strong>Gegenstand:</strong> {{ $gegenstand }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 15px; border-top: 1px solid #f1f1f1; color: #e74c3c;">
                                            <strong>Rückgabetermin:</strong> {{ $rueckgabeTermin }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <p>Bitte stelle sicher, dass das Werkzeug bis zum genannten Termin zurückgegeben oder die Leihfrist im Portal verlängert wird.</p>
                            <br>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ rtrim(config('app.url'), '/') }}/" class="button">Jetzt im Portal bearbeiten</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

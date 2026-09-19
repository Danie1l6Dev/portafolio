@php
    $replyUrl = 'mailto:'.$contactMessage->email.'?subject='.rawurlencode('Re: '.$contactMessage->subject);
    $panelUrl = route('panel.messages');
    $initial = mb_strtoupper(mb_substr(trim($contactMessage->name), 0, 1));
    $receivedAt = ($contactMessage->created_at ?? now())->timezone(config('app.timezone'))->translatedFormat('j \d\e F \d\e Y, g:i a');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="margin:0;padding:0;background-color:#eef2f6;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#091223;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">{{ $contactMessage->name }} te escribió: {{ \Illuminate\Support\Str::limit($contactMessage->subject, 80) }}</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef2f6;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #dbe3ec;">
                    {{-- Cabecera --}}
                    <tr>
                        <td style="background-color:#091223;padding:28px 32px;">
                            <p style="margin:0 0 6px;font-size:12px;letter-spacing:1.5px;text-transform:uppercase;color:#38bdf8;font-weight:700;">Portafolio · {{ config('portfolio.name') }}</p>
                            <h1 style="margin:0;font-size:22px;line-height:1.3;color:#ffffff;font-weight:700;">Tienes un nuevo mensaje</h1>
                        </td>
                    </tr>

                    {{-- Remitente --}}
                    <tr>
                        <td style="padding:28px 32px 8px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="52" valign="top">
                                        <div style="width:48px;height:48px;line-height:48px;border-radius:50%;background-color:#0369a1;color:#ffffff;text-align:center;font-size:20px;font-weight:700;">{{ $initial }}</div>
                                    </td>
                                    <td valign="middle" style="padding-left:12px;">
                                        <p style="margin:0;font-size:17px;font-weight:700;color:#091223;">{{ $contactMessage->name }}</p>
                                        <p style="margin:2px 0 0;font-size:14px;"><a href="mailto:{{ $contactMessage->email }}" style="color:#0369a1;text-decoration:none;">{{ $contactMessage->email }}</a></p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Asunto --}}
                    <tr>
                        <td style="padding:20px 32px 0;">
                            <p style="margin:0 0 4px;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#5d7085;font-weight:700;">Asunto</p>
                            <p style="margin:0;font-size:18px;line-height:1.4;font-weight:600;color:#091223;">{{ $contactMessage->subject }}</p>
                        </td>
                    </tr>

                    {{-- Mensaje --}}
                    <tr>
                        <td style="padding:20px 32px 0;">
                            <p style="margin:0 0 8px;font-size:12px;letter-spacing:1px;text-transform:uppercase;color:#5d7085;font-weight:700;">Mensaje</p>
                            <div style="background-color:#f3f6f9;border-left:4px solid #38bdf8;border-radius:8px;padding:16px 18px;font-size:15px;line-height:1.7;color:#202e40;">{!! nl2br(e($contactMessage->body)) !!}</div>
                        </td>
                    </tr>

                    {{-- Botones --}}
                    <tr>
                        <td style="padding:28px 32px 8px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius:8px;background-color:#0369a1;">
                                        <a href="{{ $replyUrl }}" style="display:inline-block;padding:13px 24px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;">Responder</a>
                                    </td>
                                    <td width="12"></td>
                                    <td style="border-radius:8px;border:1px solid #cad5e0;">
                                        <a href="{{ $panelUrl }}" style="display:inline-block;padding:12px 22px;font-size:15px;font-weight:600;color:#334357;text-decoration:none;">Ver en el panel</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Pie --}}
                    <tr>
                        <td style="padding:24px 32px 28px;">
                            <hr style="border:none;border-top:1px solid #e5ebf1;margin:0 0 16px;">
                            <p style="margin:0;font-size:13px;line-height:1.6;color:#7d8fa3;">
                                Recibido el {{ $receivedAt }}.<br>
                                También puedes responder directamente a este correo: irá a {{ $contactMessage->email }}.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

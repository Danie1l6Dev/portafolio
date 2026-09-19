Has recibido un nuevo mensaje desde tu portafolio.

Nombre: {{ $contactMessage->name }}
Correo: {{ $contactMessage->email }}
Asunto: {{ $contactMessage->subject }}

{{ $contactMessage->body }}

—
Puedes responder directamente a este correo. También lo encuentras en el panel: {{ route('panel.messages') }}

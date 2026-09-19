NUEVO MENSAJE EN TU PORTAFOLIO
==============================

De:      {{ $contactMessage->name }} <{{ $contactMessage->email }}>
Asunto:  {{ $contactMessage->subject }}

Mensaje:
--------
{{ $contactMessage->body }}

--------
Responder: {{ $contactMessage->email }} (también puedes responder a este correo)
Ver en el panel: {{ route('panel.messages') }}

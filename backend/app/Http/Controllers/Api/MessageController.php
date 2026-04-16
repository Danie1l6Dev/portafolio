<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * POST /api/v1/contact
     *
     * Recibe el formulario de contacto público y guarda el mensaje.
     * No requiere autenticación.
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        $message = Message::create($request->validated());
        $message->ip_address = $request->ip();
        $message->save();

        return response()->json([
            'message' => 'Mensaje enviado correctamente. Me pondré en contacto contigo pronto.',
            'data'    => ['id' => $message->id],
        ], 201);
    }
}

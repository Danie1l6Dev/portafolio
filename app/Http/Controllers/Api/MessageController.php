<?php

namespace App\Http\Controllers\Api;

use App\Actions\StoreContactMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMessageRequest;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    /**
     * POST /contact
     *
     * Recibe el formulario de contacto público y guarda el mensaje.
     * No requiere autenticación.
     */
    public function store(StoreMessageRequest $request, StoreContactMessage $storeContactMessage): JsonResponse
    {
        $message = $storeContactMessage->handle($request->validated(), $request->ip());

        return response()->json([
            'message' => 'Mensaje enviado correctamente. Me pondré en contacto contigo pronto.',
            'data' => ['id' => $message->id],
        ], 201);
    }
}

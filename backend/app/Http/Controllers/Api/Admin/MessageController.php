<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MessageController extends Controller
{
    /**
     * GET /api/v1/admin/messages
     *
     * Lista paginada de mensajes. Acepta ?filter=unread para solo no leídos.
     * Devuelve también el conteo de no leídos en el meta para el badge del sidebar.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Message::query()
            ->latestFirst()
            ->when(
                $request->query('filter') === 'unread',
                fn ($q) => $q->unread()
            );

        $messages  = $query->paginate(
            perPage: 20,
            page: $request->integer('page', 1)
        )->withQueryString();

        $unreadCount = Message::unread()->count();

        return response()->json([
            'data'  => MessageResource::collection($messages)->resolve(),
            'meta'  => [
                'current_page' => $messages->currentPage(),
                'last_page'    => $messages->lastPage(),
                'per_page'     => $messages->perPage(),
                'total'        => $messages->total(),
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * GET /api/v1/admin/messages/{message}
     *
     * Detalle del mensaje. Lo marca como leído automáticamente al abrirlo.
     */
    public function show(Message $message): JsonResponse
    {
        if (! $message->is_read) {
            $message->markAsRead();
        }

        return response()->json([
            'data' => MessageResource::make($message),
        ]);
    }

    /**
     * PATCH /api/v1/admin/messages/{message}/read
     *
     * Marca un mensaje específico como leído sin cargarlo completo.
     */
    public function markRead(Message $message): JsonResponse
    {
        $message->markAsRead();

        return response()->json([
            'data'    => MessageResource::make($message->fresh()),
            'message' => 'Mensaje marcado como leído.',
        ]);
    }

    /**
     * POST /api/v1/admin/messages/mark-all-read
     *
     * Marca todos los mensajes no leídos como leídos de una vez.
     */
    public function markAllRead(): JsonResponse
    {
        $count = Message::unread()->count();

        Message::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'message' => "Se marcaron {$count} mensaje(s) como leídos.",
            'count'   => $count,
        ]);
    }

    /**
     * DELETE /api/v1/admin/messages/{message}
     *
     * Elimina permanentemente un mensaje.
     */
    public function destroy(Message $message): JsonResponse
    {
        $message->delete();

        return response()->json([
            'message' => 'Mensaje eliminado correctamente.',
        ]);
    }
}

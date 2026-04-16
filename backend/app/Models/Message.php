<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * Campos que el usuario puede enviar desde el formulario de contacto.
     * is_read, read_at e ip_address se gestionan internamente, no son input del usuario.
     */
    protected $fillable = [
        'name',
        'email',
        'subject',
        'body',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ── Scopes ────────────────────────────────────────────────

    /** Mensajes que aún no han sido leídos. */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /** Más recientes primero. */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function markAsRead(): void
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }
}

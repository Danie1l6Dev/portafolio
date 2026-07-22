<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\AuthorizesContentEditors;
use App\Models\Message;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class MessageInbox extends Component
{
    use AuthorizesContentEditors;
    use WithPagination;

    public string $search = '';

    public string $filter = 'all';

    public ?int $selectedMessageId = null;

    public bool $confirmingDelete = false;

    public ?int $deletingMessageId = null;

    public string $deletingMessageSubject = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function selectMessage(int $messageId): void
    {
        $this->authorizeContentEditor();

        $message = Message::findOrFail($messageId);
        $this->selectedMessageId = $message->id;

        if (! $message->is_read) {
            $message->markAsRead();
        }

        $this->clampPage();
    }

    public function closeMessage(): void
    {
        $this->selectedMessageId = null;
    }

    public function markAsRead(int $messageId): void
    {
        $this->authorizeContentEditor();
        Message::findOrFail($messageId)->markAsRead();
        $this->clampPage();
        Flux::toast(variant: 'success', text: 'Mensaje marcado como leído.');
    }

    public function markAllAsRead(): void
    {
        $this->authorizeContentEditor();

        $count = Message::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->clampPage();
        Flux::toast(variant: 'success', text: "{$count} mensaje(s) marcados como leídos.");
    }

    public function confirmDelete(int $messageId): void
    {
        $message = Message::findOrFail($messageId);

        $this->deletingMessageId = $message->id;
        $this->deletingMessageSubject = $message->subject ?: 'Sin asunto';
        $this->confirmingDelete = true;
    }

    public function delete(): void
    {
        $this->authorizeContentEditor();

        $message = Message::findOrFail($this->deletingMessageId);
        $messageId = $message->id;
        $message->delete();

        if ($this->selectedMessageId === $messageId) {
            $this->selectedMessageId = null;
        }

        $this->cancelDelete();
        $this->clampPage();
        Flux::toast(variant: 'success', text: 'Mensaje eliminado.');
    }

    public function cancelDelete(): void
    {
        $this->reset('confirmingDelete', 'deletingMessageId', 'deletingMessageSubject');
    }

    public function render(): View
    {
        return view('livewire.admin.message-inbox', [
            'messages' => $this->messages(),
            'selectedMessage' => $this->selectedMessageId
                ? Message::find($this->selectedMessageId)
                : null,
            'unreadCount' => Message::unread()->count(),
        ]);
    }

    /** @return LengthAwarePaginator<int, Message> */
    private function messages(): LengthAwarePaginator
    {
        return Message::query()
            ->when($this->filter === 'unread', fn ($query) => $query->unread())
            ->when($this->filter === 'read', fn ($query) => $query->where('is_read', true))
            ->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%")
                    ->orWhere('body', 'like', "%{$this->search}%");
            }))
            ->latestFirst()
            ->paginate(15);
    }

    private function clampPage(): void
    {
        $lastPage = max(1, $this->messages()->lastPage());
        $currentPage = (int) $this->getPage();

        if ($currentPage > $lastPage) {
            $this->setPage($lastPage);
        }
    }
}

<?php

namespace App\Livewire\Portfolio;

use App\Actions\StoreContactMessage;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

final class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $body = '';

    public string $website = '';

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    /** @return array<string, array<int, string>> */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'body' => ['required', 'string', 'min:10', 'max:3000'],
        ];
    }

    /** @return array<string, string> */
    protected function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 2 caracteres.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'email.required' => 'El correo es obligatorio.',
            'email.email' => 'Introduce un correo válido.',
            'email.max' => 'El correo no puede superar los 150 caracteres.',
            'subject.required' => 'El asunto es obligatorio.',
            'subject.min' => 'El asunto debe tener al menos 3 caracteres.',
            'subject.max' => 'El asunto no puede superar los 150 caracteres.',
            'body.required' => 'El mensaje no puede estar vacío.',
            'body.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'body.max' => 'El mensaje no puede superar los 3000 caracteres.',
        ];
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['name', 'email', 'subject', 'body'], true)) {
            $this->resetValidation($property);
            $this->successMessage = null;
            $this->errorMessage = null;
        }
    }

    public function submit(StoreContactMessage $storeContactMessage): void
    {
        $this->resetValidation();
        $this->successMessage = null;
        $this->errorMessage = null;

        if (filled($this->website)) {
            $this->markAsSent();

            return;
        }

        $validated = $this->validate();

        try {
            $storeContactMessage->handle($validated, request()->ip());
        } catch (TooManyRequestsHttpException $exception) {
            $this->errorMessage = $exception->getMessage();
            $this->addError('form', $this->errorMessage);

            return;
        } catch (Throwable $exception) {
            report($exception);

            $this->errorMessage = 'No fue posible enviar el mensaje en este momento. Inténtalo de nuevo más tarde.';
            $this->addError('form', $this->errorMessage);

            return;
        }

        $this->markAsSent();
    }

    public function render(): View
    {
        return view('livewire.portfolio.contact-form');
    }

    private function markAsSent(): void
    {
        $this->reset(['name', 'email', 'subject', 'body', 'website', 'errorMessage']);
        $this->resetValidation();
        $this->successMessage = 'Mensaje enviado correctamente. Me pondré en contacto contigo pronto.';
    }
}

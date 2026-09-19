<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\AuthorizesContentEditors;
use App\Services\ResumeService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ResumeManager extends Component
{
    use AuthorizesContentEditors;
    use WithFileUploads;

    public ?TemporaryUploadedFile $resume = null;

    public bool $confirmingReset = false;

    public function updatedResume(): void
    {
        $this->validateOnly('resume', $this->rules(), $this->messages());
    }

    public function save(ResumeService $service): void
    {
        $this->authorizeContentEditor();

        $this->validate($this->rules(), $this->messages());

        $service->store($this->resume);

        $this->reset('resume');
        Flux::toast(variant: 'success', text: 'Hoja de vida actualizada. Ya está disponible en el portafolio.');
    }

    public function confirmReset(): void
    {
        $this->confirmingReset = true;
    }

    public function cancelReset(): void
    {
        $this->confirmingReset = false;
    }

    public function resetToDefault(ResumeService $service): void
    {
        $this->authorizeContentEditor();

        $service->reset();

        $this->confirmingReset = false;
        Flux::toast(variant: 'success', text: 'Se restauró la hoja de vida original del proyecto.');
    }

    public function render(ResumeService $service): View
    {
        return view('livewire.admin.resume-manager', [
            'current' => $service->current(),
        ]);
    }

    /** @return array<string, list<string>> */
    private function rules(): array
    {
        return [
            'resume' => [
                'required',
                'file',
                'mimes:pdf',
                'mimetypes:application/pdf',
                'max:'.config('admin.resume.max_file_kilobytes'),
            ],
        ];
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        $maxMegabytes = (int) (config('admin.resume.max_file_kilobytes') / 1024);

        return [
            'resume.required' => 'Selecciona el PDF de tu hoja de vida.',
            'resume.file' => 'El archivo seleccionado no es válido.',
            'resume.mimes' => 'La hoja de vida debe ser un archivo PDF.',
            'resume.mimetypes' => 'La hoja de vida debe ser un archivo PDF.',
            'resume.max' => "El PDF no puede superar los {$maxMegabytes} MB.",
            'resume.uploaded' => 'No se pudo subir el archivo. Revisa que no supere el tamaño permitido.',
        ];
    }
}

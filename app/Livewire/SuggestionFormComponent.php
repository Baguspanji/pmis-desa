<?php

namespace App\Livewire;

use App\Models\Suggestion;
use Livewire\Component;

class SuggestionFormComponent extends Component
{
    public $name = '';

    public $email = '';

    public $phone = '';

    public $category = 'suggestion';

    public $message = '';

    protected $rules = [
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'category' => 'required|in:criticism,suggestion',
        'message' => 'required|string|min:10',
    ];

    protected $messages = [
        'name.required' => 'Nama wajib diisi',
        'email.email' => 'Format email tidak valid',
        'category.required' => 'Kategori wajib dipilih',
        'message.required' => 'Pesan wajib diisi',
        'message.min' => 'Pesan minimal 10 karakter',
    ];

    public function submit()
    {
        $this->validate();

        Suggestion::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'category' => $this->category,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        session()->flash('message', 'Kritik dan saran Anda telah berhasil dikirim. Terima kasih!');

        $this->reset(['name', 'email', 'phone', 'message']);
        $this->category = 'suggestion';

        $this->dispatch('suggestion-submitted');
    }

    public function render()
    {
        return view('livewire.suggestion-form-component');
    }
}

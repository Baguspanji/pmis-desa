<?php

namespace App\Livewire;

use App\Models\Suggestion;
use Livewire\Component;
use Livewire\WithPagination;

class SuggestionListComponent extends Component
{
    use WithPagination;

    protected $listeners = ['suggestion-submitted' => '$refresh'];

    public $name = '';

    public $email = '';

    public $phone = '';

    public $category = 'suggestion';

    public $message = '';

    public $filterCategory = '';

    public $filterStatus = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'category' => 'required|in:criticism,suggestion',
        'message' => 'required|string|min:10',
    ];

    public function render()
    {
        $suggestions = Suggestion::query()
            ->when($this->filterCategory, function ($query) {
                $query->where('category', $this->filterCategory);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->where('status', '!=', 'draft')
            ->latest()
            ->paginate(10);

        return view('livewire.suggestion-list-component', [
            'suggestions' => $suggestions,
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\News;
use Livewire\Component;
use Livewire\WithPagination;

class NewsListComponent extends Component
{
    use WithPagination;

    public $perPage = 6;

    public function render()
    {
        $featuredNews = News::published()
            ->featured()
            ->latest('published_at')
            ->limit(2)
            ->get();

        $news = News::published()
            ->latest('published_at')
            ->paginate($this->perPage);

        return view('livewire.news-list-component', [
            'featuredNews' => $featuredNews,
            'news' => $news,
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Models\News;
use Livewire\Component;

class NewsDetailComponent extends Component
{
    public $slug;

    public $news;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->news = News::where('slug', $slug)
            ->published()
            ->firstOrFail();
    }

    public function render()
    {
        $relatedNews = News::published()
            ->where('id', '!=', $this->news->id)
            ->where('category', $this->news->category)
            ->latest('published_at')
            ->limit(3)
            ->get();

        if ($relatedNews->count() < 3) {
            $additionalNews = News::published()
                ->where('id', '!=', $this->news->id)
                ->whereNotIn('id', $relatedNews->pluck('id'))
                ->latest('published_at')
                ->limit(3 - $relatedNews->count())
                ->get();

            $relatedNews = $relatedNews->merge($additionalNews);
        }

        return view('livewire.news-detail-component', [
            'relatedNews' => $relatedNews,
        ]);
    }
}

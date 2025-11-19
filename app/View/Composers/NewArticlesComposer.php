<?php

namespace App\View\Composers;

use App\Models\Post;
use Illuminate\View\View;

class NewArticlesComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $newArticles = Post::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $view->with('newArticles', $newArticles);
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of sports news posts.
     */
    public function indexSportsNews(Request $request)
    {
        $posts = Post::where('type', 'tin-the-thao')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tin-the-thao', compact('posts'));
    }

    /**
     * Display a listing of football predictions posts.
     */
    public function indexPredictions(Request $request)
    {
        $posts = Post::where('type', 'nhan-dinh-bong-da')
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('nhan-dinh-bong-da', compact('posts'));
    }

    /**
     * Display the specified post.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment views
        $post->increment('views');

        // Get related posts
        $relatedPosts = Post::where('type', $post->type)
            ->where('status', 'published')
            ->where('id', '!=', $post->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('post-detail', compact('post', 'relatedPosts'));
    }
}


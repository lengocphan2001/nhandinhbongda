<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'tin-the-thao');
        $posts = Post::where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.posts.index', compact('posts', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'tin-the-thao');
        return view('admin.posts.create', compact('type'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:tin-the-thao,nhan-dinh-bong-da',
            'status' => 'required|in:draft,published',
        ]);

        $slug = Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Post::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = $slug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $uniqueSlug;
        $validated['user_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        Post::create($validated);

        return redirect()->route('admin.posts.index', ['type' => $validated['type']])
            ->with('success', 'Bài viết đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:tin-the-thao,nhan-dinh-bong-da',
            'status' => 'required|in:draft,published',
        ]);

        // Update slug if title changed
        if ($post->title !== $validated['title']) {
            $slug = Str::slug($validated['title']);
            $uniqueSlug = $slug;
            $counter = 1;
            while (Post::where('slug', $uniqueSlug)->where('id', '!=', $post->id)->exists()) {
                $uniqueSlug = $slug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $uniqueSlug;
        }

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update($validated);

        return redirect()->route('admin.posts.index', ['type' => $validated['type']])
            ->with('success', 'Bài viết đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index', ['type' => $post->type])
            ->with('success', 'Bài viết đã được xóa thành công!');
    }

    /**
     * Handle image upload from TinyMCE.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('posts/images', 'public');
            // Use asset() to generate absolute URL
            $url = asset(Storage::url($path));

            return response()->json([
                'location' => $url
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}

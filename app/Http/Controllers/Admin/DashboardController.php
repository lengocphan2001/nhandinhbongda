<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $stats = [
            'tin-the-thao' => Post::where('type', 'tin-the-thao')->count(),
            'nhan-dinh-bong-da' => Post::where('type', 'nhan-dinh-bong-da')->count(),
            'total-views' => Post::sum('views'),
        ];
        
        $recentPosts = Post::orderBy('created_at', 'desc')->limit(10)->get();
        
        return view('admin.dashboard', compact('stats', 'recentPosts'));
    }
}

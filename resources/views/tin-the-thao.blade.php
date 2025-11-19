@extends('layouts.app')

@section('title', 'Tin Thể Thao - XOILAC TV')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="bg-gray-800 rounded-lg p-6">
    <h1 class="text-2xl font-bold text-white mb-6 uppercase">Tin Thể Thao</h1>
    
    <!-- News List -->
    <div class="space-y-6">
        @forelse($posts as $post)
        <article class="bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors">
            <a href="{{ route('post.show', $post->slug) }}" class="block">
                <div class="flex gap-4">
                    @if($post->featured_image)
                    <div class="w-32 h-24 bg-gray-600 rounded flex-shrink-0 overflow-hidden">
                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                    </div>
                    @else
                    <div class="w-32 h-24 bg-gray-600 rounded flex-shrink-0">
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                            </svg>
                        </div>
                    </div>
                    @endif
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-green-400 text-xs">Tin Thể Thao</span>
                            <span class="text-gray-400 text-xs">•</span>
                            <span class="text-gray-400 text-xs">{{ $post->created_at->format('d/m/Y') }}</span>
                            @if($post->views > 0)
                            <span class="text-gray-400 text-xs">•</span>
                            <span class="text-gray-400 text-xs">{{ number_format($post->views) }} lượt xem</span>
                            @endif
                        </div>
                        <h2 class="text-white font-semibold text-lg mb-2 hover:text-green-400 transition-colors">
                            {{ $post->title }}
                        </h2>
                        @if($post->excerpt)
                        <p class="text-gray-300 text-sm leading-relaxed">
                            {{ $post->excerpt }}
                        </p>
                        @endif
                    </div>
                </div>
            </a>
        </article>
        @empty
        <div class="bg-gray-700 rounded-lg p-8 text-center">
            <p class="text-gray-400 text-lg">Chưa có tin thể thao nào được đăng tải.</p>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($posts->hasPages())
    <div class="mt-6">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection


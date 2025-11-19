@extends('layouts.app')

@section('title', $post->title . ' - XOILAC TV')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="bg-gray-800 rounded-lg p-3 sm:p-4 md:p-6 overflow-hidden">
    <!-- Breadcrumb -->
    <nav class="mb-3 sm:mb-4 text-xs sm:text-sm truncate">
        <a href="{{ route('bang-xep-hang') }}" class="text-green-400 hover:text-green-300">Trang chủ</a>
        <span class="text-gray-500 mx-1 sm:mx-2">/</span>
        <a href="{{ $post->type == 'tin-the-thao' ? route('tin-the-thao') : route('nhan-dinh-bong-da') }}" class="text-green-400 hover:text-green-300">
            {{ $post->type == 'tin-the-thao' ? 'Tin Thể Thao' : 'Nhận Định Bóng Đá' }}
        </a>
        <span class="text-gray-500 mx-1 sm:mx-2">/</span>
        <span class="text-gray-400 truncate inline-block max-w-xs sm:max-w-none">{{ $post->title }}</span>
    </nav>
    
    <!-- Article Header -->
    <header class="mb-4 sm:mb-6">
        <div class="flex flex-wrap items-center gap-1 sm:gap-2 mb-3 sm:mb-4 text-xs sm:text-sm">
            <span class="text-green-400">{{ $post->type == 'tin-the-thao' ? 'Tin Thể Thao' : 'Nhận Định Bóng Đá' }}</span>
            <span class="text-gray-500">•</span>
            <span class="text-gray-400">{{ $post->created_at->format('d/m/Y H:i') }}</span>
            @if($post->views > 0)
            <span class="text-gray-500">•</span>
            <span class="text-gray-400">{{ number_format($post->views) }} lượt xem</span>
            @endif
        </div>
        
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white mb-3 sm:mb-4 break-words">{{ $post->title }}</h1>
        
        @if($post->excerpt)
        <p class="text-gray-300 text-sm sm:text-base md:text-lg leading-relaxed mb-3 sm:mb-4">{{ $post->excerpt }}</p>
        @endif
        
        @if($post->featured_image)
        <div class="mb-4 sm:mb-6">
            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full rounded-lg">
        </div>
        @endif
    </header>
    
    <!-- Article Content -->
    <article class="article-content">
        <div class="text-gray-300 leading-relaxed">
            {!! $post->content !!}
        </div>
    </article>
    
    @push('styles')
    <style>
        .article-content {
            color: #d1d5db;
            line-height: 1.75;
        }
        
        .article-content h1,
        .article-content h2,
        .article-content h3,
        .article-content h4,
        .article-content h5,
        .article-content h6 {
            color: #ffffff;
            font-weight: 700;
            margin-top: 1.5em;
            margin-bottom: 0.75em;
            line-height: 1.3;
        }
        
        .article-content h1 {
            font-size: 2em;
        }
        
        .article-content h2 {
            font-size: 1.75em;
        }
        
        .article-content h3 {
            font-size: 1.5em;
        }
        
        .article-content h4 {
            font-size: 1.25em;
        }
        
        .article-content p {
            margin-bottom: 1em;
            line-height: 1.75;
        }
        
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1.5em 0;
        }
        
        .article-content a {
            color: #4ade80;
            text-decoration: underline;
        }
        
        .article-content a:hover {
            color: #22c55e;
        }
        
        .article-content ul,
        .article-content ol {
            margin: 1em 0;
            padding-left: 2em;
        }
        
        .article-content li {
            margin-bottom: 0.5em;
        }
        
        .article-content ul {
            list-style-type: disc;
        }
        
        .article-content ol {
            list-style-type: decimal;
        }
        
        .article-content blockquote {
            border-left: 4px solid #4ade80;
            padding-left: 1em;
            margin: 1.5em 0;
            font-style: italic;
            color: #9ca3af;
        }
        
        .article-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5em 0;
        }
        
        .article-content table th,
        .article-content table td {
            border: 1px solid #4b5563;
            padding: 0.75em;
        }
        
        .article-content table th {
            background-color: #374151;
            color: #ffffff;
            font-weight: 600;
        }
        
        .article-content table td {
            background-color: #1f2937;
        }
        
        .article-content code {
            background-color: #374151;
            color: #fbbf24;
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
            font-size: 0.9em;
        }
        
        .article-content pre {
            background-color: #1f2937;
            color: #d1d5db;
            padding: 1em;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin: 1.5em 0;
        }
        
        .article-content pre code {
            background-color: transparent;
            color: inherit;
            padding: 0;
        }
        
        .article-content strong,
        .article-content b {
            font-weight: 700;
            color: #ffffff;
        }
        
        .article-content em,
        .article-content i {
            font-style: italic;
        }
        
        .article-content hr {
            border: none;
            border-top: 1px solid #4b5563;
            margin: 2em 0;
        }
        
        .article-content iframe {
            max-width: 100%;
            margin: 1.5em 0;
        }
        
        .article-content *:first-child {
            margin-top: 0;
        }
        
        .article-content *:last-child {
            margin-bottom: 0;
        }
        
        /* Text alignment */
        .article-content [style*="text-align: left"],
        .article-content .text-left {
            text-align: left;
        }
        
        .article-content [style*="text-align: center"],
        .article-content .text-center {
            text-align: center;
        }
        
        .article-content [style*="text-align: right"],
        .article-content .text-right {
            text-align: right;
        }
        
        .article-content [style*="text-align: justify"],
        .article-content .text-justify {
            text-align: justify;
        }
        
        /* Image alignment */
        .article-content img[style*="float: left"],
        .article-content img[style*="float:left"] {
            float: left;
            margin-right: 1em;
            margin-bottom: 1em;
        }
        
        .article-content img[style*="float: right"],
        .article-content img[style*="float:right"] {
            float: right;
            margin-left: 1em;
            margin-bottom: 1em;
        }
        
        .article-content img[style*="display: block"],
        .article-content img[style*="display:block"] {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* Clear floats */
        .article-content::after {
            content: "";
            display: table;
            clear: both;
        }
        
        /* Text colors */
        .article-content [style*="color:"] {
            /* Preserve inline color styles from TinyMCE */
        }
        
        /* Font sizes */
        .article-content [style*="font-size:"] {
            /* Preserve inline font-size styles from TinyMCE */
        }
        
        /* Background colors */
        .article-content [style*="background-color:"] {
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
        }
    </style>
    @endpush
    
    <!-- Related Posts -->
    @if($relatedPosts->count() > 0)
    <div class="mt-8 sm:mt-12 pt-6 sm:pt-8 border-t border-gray-700">
        <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-white mb-4 sm:mb-6">Bài viết liên quan</h2>
        <div class="space-y-3 sm:space-y-4">
            @foreach($relatedPosts as $relatedPost)
            <article class="bg-gray-700 rounded-lg p-3 sm:p-4 hover:bg-gray-600 transition-colors">
                <a href="{{ route('post.show', $relatedPost->slug) }}" class="block">
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                        @if($relatedPost->featured_image)
                        <div class="w-full sm:w-24 h-32 sm:h-20 bg-gray-600 rounded flex-shrink-0 overflow-hidden">
                            <img src="{{ Storage::url($relatedPost->featured_image) }}" alt="{{ $relatedPost->title }}" class="w-full h-full object-cover">
                        </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="text-white font-semibold mb-1 hover:text-green-400 transition-colors text-sm sm:text-base line-clamp-2">
                                {{ $relatedPost->title }}
                            </h3>
                            <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs text-gray-400">
                                <span>{{ $relatedPost->created_at->format('d/m/Y') }}</span>
                                @if($relatedPost->views > 0)
                                <span>•</span>
                                <span>{{ number_format($relatedPost->views) }} lượt xem</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection


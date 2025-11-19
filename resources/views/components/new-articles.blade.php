@php
use Illuminate\Support\Facades\Storage;
@endphp

<div class="bg-gray-800 rounded-lg p-4">
    <h2 class="text-white text-xl font-bold mb-4 uppercase">BÀI VIẾT MỚI</h2>
    
    <div class="space-y-0">
        @forelse($newArticles ?? [] as $article)
        <article class="flex gap-3 pb-4 mb-4 border-b border-gray-700 last:border-0 last:mb-0">
            <a href="{{ route('post.show', $article->slug) }}" class="flex gap-3 w-full">
                <div class="w-24 h-20 bg-gray-700 rounded flex-shrink-0 relative overflow-hidden">
                    @if($article->featured_image)
                        <img src="{{ Storage::url($article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-r from-gray-600 to-gray-700">
                            @if($article->type == 'nhan-dinh-bong-da')
                                <div class="text-center">
                                    <div class="text-orange-400 text-xs font-bold">{{ $article->created_at->format('H:i') }}</div>
                                    <div class="text-orange-400 text-xs">{{ $article->created_at->format('d-m-Y') }}</div>
                                </div>
                            @else
                                <svg class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                </svg>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-green-400 text-xs mb-1">
                        {{ $article->type == 'tin-the-thao' ? 'Tin Thể Thao' : 'Nhận Định Bóng Đá' }} {{ $article->created_at->format('d/m/Y') }}
                    </div>
                    <h3 class="text-white text-sm leading-tight line-clamp-2 hover:text-green-400 transition-colors">
                        {{ $article->title }}
                    </h3>
                </div>
            </a>
        </article>
        @empty
        <div class="py-4 text-center">
            <p class="text-gray-400 text-sm">Chưa có bài viết nào</p>
        </div>
        @endforelse
    </div>
</div>


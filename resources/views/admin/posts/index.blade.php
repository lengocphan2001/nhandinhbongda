@extends('layouts.admin')

@section('title', $type == 'tin-the-thao' ? 'Tin Thể Thao' : 'Nhận Định Bóng Đá')
@section('page-title', $type == 'tin-the-thao' ? 'Quản Lý Tin Thể Thao' : 'Quản Lý Nhận Định Bóng Đá')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div class="flex space-x-2">
        <a href="{{ route('admin.posts.index', ['type' => 'tin-the-thao']) }}" class="px-4 py-2 rounded-lg {{ $type == 'tin-the-thao' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Tin Thể Thao
        </a>
        <a href="{{ route('admin.posts.index', ['type' => 'nhan-dinh-bong-da']) }}" class="px-4 py-2 rounded-lg {{ $type == 'nhan-dinh-bong-da' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
            Nhận Định Bóng Đá
        </a>
    </div>
    <a href="{{ route('admin.posts.create', ['type' => $type]) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors">
        + Tạo Bài Viết Mới
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lượt xem</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($posts as $post)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                            @if($post->excerpt)
                                <div class="text-sm text-gray-500 mt-1">{{ Str::limit($post->excerpt, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $post->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $post->status == 'published' ? 'Đã xuất bản' : 'Bản nháp' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ number_format($post->views) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $post->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.posts.edit', $post) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Sửa</a>
                            <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Chưa có bài viết nào. <a href="{{ route('admin.posts.create', ['type' => $type]) }}" class="text-green-600 hover:text-green-800">Tạo bài viết mới</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($posts->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection


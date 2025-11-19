@extends('layouts.admin')

@section('title', 'Cấu Hình API')
@section('page-title', 'Cấu Hình API')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <form action="{{ route('admin.api-config.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="SOCCERSAPI_USER" class="block text-sm font-medium text-gray-700 mb-2">
                    SoccersAPI User
                </label>
                <input 
                    type="text" 
                    id="SOCCERSAPI_USER" 
                    name="SOCCERSAPI_USER" 
                    value="{{ old('SOCCERSAPI_USER', $config['SOCCERSAPI_USER'] ?? env('SOCCERSAPI_USER', '')) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('SOCCERSAPI_USER') border-red-500 @enderror"
                    placeholder="Nhập SoccersAPI User"
                >
                @error('SOCCERSAPI_USER')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="SOCCERSAPI_TOKEN" class="block text-sm font-medium text-gray-700 mb-2">
                    SoccersAPI Token
                </label>
                <input 
                    type="text" 
                    id="SOCCERSAPI_TOKEN" 
                    name="SOCCERSAPI_TOKEN" 
                    value="{{ old('SOCCERSAPI_TOKEN', $config['SOCCERSAPI_TOKEN'] ?? env('SOCCERSAPI_TOKEN', '')) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('SOCCERSAPI_TOKEN') border-red-500 @enderror"
                    placeholder="Nhập SoccersAPI Token"
                >
                @error('SOCCERSAPI_TOKEN')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Lưu ý</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p>Thay đổi cấu hình API sẽ ảnh hưởng đến tất cả các màn hình sử dụng API. Vui lòng đảm bảo thông tin chính xác trước khi lưu.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Lưu Cấu Hình
                </button>
            </div>
        </div>
    </form>
</div>
@endsection


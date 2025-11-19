@extends('layouts.admin')

@section('title', 'Tạo Bài Viết Mới')
@section('page-title', 'Tạo Bài Viết Mới')

@push('styles')
<script src="https://cdn.tiny.cloud/1/xhvi99zf95ueinybzalp9vwc7yaolsr1rxibrza2dzwb9c8e/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@endpush

@section('content')
<form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    
    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('title') border-red-500 @enderror">
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Mô tả ngắn</label>
            <textarea id="excerpt" name="excerpt" rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('excerpt') border-red-500 @enderror">{{ old('excerpt') }}</textarea>
            @error('excerpt')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Nội dung *</label>
            <textarea id="content" name="content" rows="15"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('content') border-red-500 @enderror">{{ old('content') }}</textarea>
            @error('content')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh đại diện</label>
            <input type="file" id="featured_image" name="featured_image" accept="image/*" onchange="previewImage(this)"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('featured_image') border-red-500 @enderror">
            <div id="imagePreview" class="mt-2 hidden">
                <img id="previewImg" src="" alt="Preview" class="max-h-48 rounded">
            </div>
            @error('featured_image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Trạng thái *</label>
            <select id="status" name="status" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 @error('status') border-red-500 @enderror">
                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.posts.index', ['type' => $type]) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Hủy
            </a>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Lưu Bài Viết
            </button>
        </div>
    </div>
</form>

@push('scripts')
<script>
let tinyMceReady = false;

tinymce.init({
    selector: '#content',
    height: 500,
    menubar: false,
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
        'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | ' +
        'bold italic forecolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | image | help',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    language: 'vi',
    branding: false,
    relative_urls: false,
    remove_script_host: false,
    convert_urls: true,
    document_base_url: '{{ url("/") }}/',
    setup: function(editor) {
        editor.on('init', function() {
            tinyMceReady = true;
        });
    },
    images_upload_url: '{{ route("admin.upload-image") }}',
    images_upload_handler: function (blobInfo, progress) {
        return new Promise(function (resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '{{ route("admin.upload-image") }}');
            
            var token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (token) {
                xhr.setRequestHeader('X-CSRF-TOKEN', token);
            }
            
            xhr.upload.onprogress = function (e) {
                progress(e.loaded / e.total * 100);
            };
            
            xhr.onload = function () {
                if (xhr.status === 403) {
                    reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                    return;
                }
                
                if (xhr.status < 200 || xhr.status >= 300) {
                    reject('HTTP Error: ' + xhr.status);
                    return;
                }
                
                var json = JSON.parse(xhr.responseText);
                
                if (!json || typeof json.location != 'string') {
                    reject('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                
                resolve(json.location);
            };
            
            xhr.onerror = function () {
                reject('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
            };
            
            var formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            
            xhr.send(formData);
        });
    },
    automatic_uploads: true,
    file_picker_types: 'image',
    file_picker_callback: function(cb, value, meta) {
        var input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        
        input.onchange = function() {
            var file = this.files[0];
            var reader = new FileReader();
            
            reader.onload = function() {
                var id = 'blobid' + (new Date()).getTime();
                var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                var base64 = reader.result.split(',')[1];
                var blobInfo = blobCache.create(id, file, base64);
                blobCache.add(blobInfo);
                
                cb(blobInfo.blobUri(), { title: file.name });
            };
            reader.readAsDataURL(file);
        };
        
        input.click();
    }
});

// Sync TinyMCE content before form submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default submit first
            
            // Sync TinyMCE content
            if (tinyMceReady && tinymce.get('content')) {
                tinymce.get('content').save();
            }
            
            // Validate content
            const contentTextarea = document.getElementById('content');
            const contentValue = contentTextarea ? contentTextarea.value.trim() : '';
            
            if (!contentValue) {
                alert('Vui lòng nhập nội dung bài viết!');
                if (tinyMceReady && tinymce.get('content')) {
                    tinymce.get('content').focus();
                }
                return false;
            }
            
            // Wait a bit to ensure content is saved, then submit
            setTimeout(function() {
                form.submit();
            }, 300);
        });
    }
});

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
    }
}
</script>
@endpush
@endsection


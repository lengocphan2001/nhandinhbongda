<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'XOILAC TV - Tin tức thể thao')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-900 text-white font-sans min-h-screen">
    @include('components.header')
    
    <main class="container mx-auto px-4 py-6 max-w-7xl overflow-x-hidden">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="flex-1 min-w-0">
                @yield('content')
            </div>
            <aside class="w-full lg:w-80 flex-shrink-0">
                @include('components.new-articles')
            </aside>
        </div>
    </main>
    
    @stack('scripts')
</body>
</html>


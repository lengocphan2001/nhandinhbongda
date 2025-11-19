<header class="border-t-2 border-green-600 border-b border-black">
    <div class="container mx-auto">
        <div class="flex items-center">
            <!-- Logo Section (Left - White Background) -->
            <div class="flex items-center">
                <a href="{{ route('bang-xep-hang') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="XOILAC TV" class="w-auto h-12">
                </a>
            </div>
            
            <!-- Navigation Menu (Right - Dark Background) -->
            <nav class="flex-1 px-4 py-4">
                <ul class="flex items-center gap-0 flex-wrap">
                    <li>
                        <a href="{{ route('bang-xep-hang') }}" class="px-4 py-2 uppercase text-sm font-medium transition-colors {{ request()->routeIs('bang-xep-hang') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            BẢNG XẾP HẠNG
                        </a>
                    </li>
                    <li class="text-white/40 text-lg">/</li>
                    <li>
                        <a href="{{ route('lich-thi-dau') }}" class="px-4 py-2 uppercase text-sm font-medium transition-colors {{ request()->routeIs('lich-thi-dau') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            LỊCH THI ĐẤU
                        </a>
                    </li>
                    <li class="text-white/40 text-lg">/</li>
                    <li>
                        <a href="{{ route('ket-qua') }}" class="px-4 py-2 uppercase text-sm font-medium transition-colors {{ request()->routeIs('ket-qua') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            KẾT QUẢ
                        </a>
                    </li>
                    <li class="text-white/40 text-lg">/</li>
                    <li>
                        <a href="{{ route('top-ghi-ban') }}" class="px-4 py-2 uppercase text-sm font-medium transition-colors {{ request()->routeIs('top-ghi-ban') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            TOP GHI BÀN
                        </a>
                    </li>
                    <li class="text-white/40 text-lg">/</li>
                    <li>
                        <a href="{{ route('tin-the-thao') }}" class="px-4 py-2 uppercase text-sm font-medium transition-colors {{ request()->routeIs('tin-the-thao') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            TIN THỂ THAO
                        </a>
                    </li>
                    <li class="text-white/40 text-lg">/</li>
                    <li>
                        <a href="{{ route('nhan-dinh-bong-da') }}" class="px-4 py-2 uppercase text-sm font-medium transition-colors {{ request()->routeIs('nhan-dinh-bong-da') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            NHẬN ĐỊNH BÓNG ĐÁ
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>


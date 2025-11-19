<header class="border-t-2 border-green-600 border-b border-black">
    <div class="container mx-auto px-2 sm:px-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center">
            <!-- Logo Section (Left - White Background) -->
            <div class="flex items-center justify-between justify-start py-2 sm:py-0">
                <a href="{{ route('bang-xep-hang') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="XOILAC TV" class="w-12 h-12">
                </a>
                <!-- Mobile Menu Toggle -->
                <button id="mobileMenuToggle" class="sm:hidden text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Navigation Menu (Right - Dark Background) -->
            <nav id="mainNav" class="hidden sm:flex flex-1 px-2 sm:px-4 py-2 sm:py-4 w-full sm:w-auto">
                <ul class="flex flex-col sm:flex-row items-start sm:items-center gap-0 flex-wrap">
                    <li class="w-full sm:w-auto">
                        <a href="{{ route('bang-xep-hang') }}" class="block px-2 sm:px-4 py-2 uppercase text-xs sm:text-sm font-medium transition-colors {{ request()->routeIs('bang-xep-hang') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            BẢNG XẾP HẠNG
                        </a>
                    </li>
                    <li class="text-white/40 text-base sm:text-lg hidden sm:block">/</li>
                    <li class="w-full sm:w-auto">
                        <a href="{{ route('lich-thi-dau') }}" class="block px-2 sm:px-4 py-2 uppercase text-xs sm:text-sm font-medium transition-colors {{ request()->routeIs('lich-thi-dau') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            LỊCH THI ĐẤU
                        </a>
                    </li>
                    <li class="text-white/40 text-base sm:text-lg hidden sm:block">/</li>
                    <li class="w-full sm:w-auto">
                        <a href="{{ route('ket-qua') }}" class="block px-2 sm:px-4 py-2 uppercase text-xs sm:text-sm font-medium transition-colors {{ request()->routeIs('ket-qua') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            KẾT QUẢ
                        </a>
                    </li>
                    <li class="text-white/40 text-base sm:text-lg hidden sm:block">/</li>
                    <li class="w-full sm:w-auto">
                        <a href="{{ route('top-ghi-ban') }}" class="block px-2 sm:px-4 py-2 uppercase text-xs sm:text-sm font-medium transition-colors {{ request()->routeIs('top-ghi-ban') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            TOP GHI BÀN
                        </a>
                    </li>
                    <li class="text-white/40 text-base sm:text-lg hidden sm:block">/</li>
                    <li class="w-full sm:w-auto">
                        <a href="{{ route('tin-the-thao') }}" class="block px-2 sm:px-4 py-2 uppercase text-xs sm:text-sm font-medium transition-colors {{ request()->routeIs('tin-the-thao') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            TIN THỂ THAO
                        </a>
                    </li>
                    <li class="text-white/40 text-base sm:text-lg hidden sm:block">/</li>
                    <li class="w-full sm:w-auto">
                        <a href="{{ route('nhan-dinh-bong-da') }}" class="block px-2 sm:px-4 py-2 uppercase text-xs sm:text-sm font-medium transition-colors {{ request()->routeIs('nhan-dinh-bong-da') ? 'text-teal-400' : 'text-white hover:text-green-400' }}">
                            NHẬN ĐỊNH BÓNG ĐÁ
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('hidden');
        });
    }
});
</script>
@endpush


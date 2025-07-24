<style>
[x-cloak] { display: none !important; }
body {
    background-color: aliceblue;
}
</style>

<header class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 relative" x-data="{ open: false }" :class="{'overflow-hidden': open}">
            
            <!-- Brand -->
            <div class="flex items-center">
                <span class="text-xl font-bold text-gray-900 cursor-pointer">
                    <img src="{{ asset('images/logo2.png') }}" alt="Maritime Smart Care360" class="w-32 h-auto" onclick="window.location.href='{{ route('dashboard') }}'">
                </span>
            </div>

            <!-- Right (Desktop Menu) -->
            <div class="hidden sm:flex items-center space-x-4">
                <span class="text-gray-700">Admin</span>
                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">管理者</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm flex items-center">
                        ログアウト
                    </button>
                </form>
            </div>

            <!-- Hamburger (Mobile) -->
            <button class="sm:hidden text-gray-600 hover:text-gray-900 focus:outline-none z-50" 
                    @click="open = !open" style="z-index: 0;">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Background Overlay -->
            <div x-show="open" x-cloak
                 x-transition.opacity
                 class="fixed inset-0 bg-black bg-opacity-50 z-30"
                 @click="open = false">
            </div>

            <!-- Full-Height Slide-out Drawer -->
            <div class="fixed top-0 right-0 h-screen z-50 w-64 bg-white shadow-2xl transform transition-transform duration-300 ease-in-out z-40"
                 :class="open ? 'translate-x-0' : 'translate-x-full'"
                 x-show="open" x-cloak
                 @click.away="open = false">
                <div class="flex flex-col h-full p-6 space-y-6">
                    <!-- Close Button -->
                    <button class="self-end text-gray-600 hover:text-gray-900" @click="open = false">✕</button>

                    <!-- Menu Content -->
                    <div class="flex flex-col space-y-4">
                        <span class="text-gray-700 text-lg">Admin</span>
                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded self-start">管理者</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm flex items-center">
                                ログアウト
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<script src="//unpkg.com/alpinejs" defer></script>

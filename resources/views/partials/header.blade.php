<style>
[x-cloak] { display: none !important; }
body {
    background-color: aliceblue;
}
</style>

<div x-data="{ open: false, profileModal: false }" :class="{'overflow-hidden': open}">
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 relative">
                
                <!-- Brand -->
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-900 cursor-pointer">
                        <img src="{{ asset('images/logo2.png') }}" alt="Maritime Smart Care360" class="w-32 h-auto" onclick="window.location.href='{{ route('dashboard') }}'">
                    </span>
                </div>

                <!-- Right (Desktop Menu) -->
                <div class="hidden sm:flex items-center space-x-4">
                    
                    <!-- Profile Icon -->
                    <div class="relative">
                        <button @click="profileModal = true" class="flex items-center space-x-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-sm">{{ Auth::user()->name }}</span>
                        </button>
                    </div>
                    
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
                        <div class="flex flex-col space-y-4">
                            <div class="flex items-center space-x-2 pb-4 border-b">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                            </div>
                        </div>
                        <!-- Menu Content -->
                        <div class="flex flex-col space-y-4">                            
                            <!-- Profile Section in Mobile Menu -->
                            
                            <button @click="profileModal = true; open = false" class="text-gray-600 hover:text-gray-900 text-sm flex items-center">
                                パスワード変更
                            </button>
                            
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

    <!-- Password Change Modal -->
    <div x-show="profileModal" x-cloak
         x-transition.opacity
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
         @click.away="profileModal = false">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6" @click.stop>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">パスワード変更</h3>
                <button @click="profileModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('change-password') }}" 
                  x-data="{ 
                      showCurrentPassword: false, 
                      showNewPassword: false, 
                      showConfirmPassword: false,
                      loading: false,
                      message: '',
                      messageType: ''
                  }"
                  @submit.prevent="submitForm()">
                @csrf
                
                <!-- Notification Message -->
                <div x-show="message" 
                     x-cloak
                     x-transition
                     :class="messageType === 'success' ? 'mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded' : 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded'"
                     x-text="message">
                </div>

                <!-- Current Password -->
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">現在のパスワード</label>
                    <div class="relative">
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               :type="showCurrentPassword ? 'text' : 'password'"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        <button type="button" 
                                @click="showCurrentPassword = !showCurrentPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showCurrentPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showCurrentPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">新しいパスワード</label>
                    <div class="relative">
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               :type="showNewPassword ? 'text' : 'password'"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required
                               minlength="8">
                        <button type="button" 
                                @click="showNewPassword = !showNewPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showNewPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showNewPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                        <div class="text-xs text-gray-500 mt-1">パスワードは最低8文字である必要があります。</div>   
                </div>

                <!-- Confirm New Password -->
                <div class="mb-6">
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">新しいパスワード（確認）</label>
                    <div class="relative">
                        <input type="password" 
                               id="new_password_confirmation" 
                               name="new_password_confirmation" 
                               :type="showConfirmPassword ? 'text' : 'password'"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               required>
                        <button type="button" 
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg x-show="!showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showConfirmPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex space-x-3">
                    <button type="submit" 
                            :disabled="loading"
                            class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading">パスワード変更</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            処理中...
                        </span>
                    </button>
                    <button type="button" 
                            @click="profileModal = false"
                            class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        キャンセル
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="//unpkg.com/alpinejs" defer></script>

<script>
function submitForm() {
    const form = event.target;
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const messageDiv = form.querySelector('[x-show="message"]');
    
    // Get Alpine.js component
    const alpineComponent = Alpine.$data(form);
    
    // Set loading state
    alpineComponent.loading = true;
    alpineComponent.message = '';
    
    // Client-side validation
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('new_password_confirmation');
    
    if (newPassword.length < 8) {
        alpineComponent.message = 'パスワードは最低8文字である必要があります。';
        alpineComponent.messageType = 'error';
        alpineComponent.loading = false;
        return;
    }
    
    if (newPassword !== confirmPassword) {
        alpineComponent.message = 'パスワードが一致しません。';
        alpineComponent.messageType = 'error';
        alpineComponent.loading = false;
        return;
    }
    
    fetch('{{ route("change-password") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        alpineComponent.loading = false;
        
        if (data.success) {
            alpineComponent.message = data.message;
            alpineComponent.messageType = 'success';
            
            // Clear form
            form.reset();
            
            // Close modal after 2 seconds
            setTimeout(() => {
                alpineComponent.profileModal = false;
                alpineComponent.message = '';
            }, 2000);
        } else {
            alpineComponent.message = data.message;
            alpineComponent.messageType = 'error';
        }
    })
    .catch(error => {
        alpineComponent.loading = false;
        alpineComponent.message = 'エラーが発生しました。もう一度お試しください。';
        alpineComponent.messageType = 'error';
        console.error('Error:', error);
    });
}
</script>


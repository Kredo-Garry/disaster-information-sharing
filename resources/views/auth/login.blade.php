<x-guest-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center relative fixed inset-0" 
         style="background-image: url('https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&q=80&w=1974');">
        
        <div class="absolute inset-0 bg-black bg-opacity-40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md mx-auto px-6">
            
            <div class="bg-white bg-opacity-10 backdrop-blur-xl border border-white border-opacity-20 rounded-[2.5rem] shadow-2xl p-8 md:p-10 text-white">
                
                <div class="flex justify-center mb-6">
                    <div class="logo-bounce">
                        <svg width="120" height="120" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="20" cy="20" r="20" fill="#2563EB" />
                            
                            <g transform="translate(4, 8)">
                                <path d="M7 16.5C7 17.3284 7.67157 18 8.5 18H10.5V15H8.5C7.67157 15 7 15.6716 7 16.5Z" fill="white"/>
                                <path d="M3 13.5V10.5C3 9.39543 3.89543 8.5 5 8.5H7L15 5V19L7 15.5H5C3.89543 15.5 3 14.6046 3 13.5Z" fill="#2563EB" stroke="white" stroke-width="1" stroke-linejoin="round"/>
                                <path d="M15 5C17.2091 5 19 8.13401 19 12C19 15.866 17.2091 19 15 19" fill="white" stroke="white" stroke-width="1"/>
                                
                                <path d="M20 9C20.8 10 21.2 11 21.2 12C21.2 13 20.8 14 20 15" stroke="white" stroke-width="1.2" stroke-linecap="round">
                                    <animate attributeName="opacity" values="0.4;1;0.4" dur="2s" repeatCount="indefinite" />
                                </path>
                                <path d="M22.5 7.5C23.7 9 24.3 10.5 24.3 12C24.3 13.5 23.7 15 22.5 16.5" stroke="white" stroke-width="1.2" stroke-linecap="round">
                                    <animate attributeName="opacity" values="0.3;1;0.3" dur="2s" begin="0.3s" repeatCount="indefinite" />
                                </path>
                                <path d="M25 6C26.5 8 27 10 27 12C27 14 26.5 16 25 18" stroke="white" stroke-width="1.2" stroke-linecap="round">
                                    <animate attributeName="opacity" values="0.2;1;0.2" dur="2s" begin="0.6s" repeatCount="indefinite" />
                                </path>
                            </g>
                        </svg>
                    </div>
                </div>

                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold tracking-tight">Disaster Info</h2>
                    <p class="text-blue-100 opacity-80 text-sm mt-2">Sign in to stay informed</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-blue-100 mb-2">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-5 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl focus:ring-4 focus:ring-blue-500 text-white placeholder-blue-200 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-blue-100 mb-2">Password</label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-5 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl focus:ring-4 focus:ring-blue-500 text-white placeholder-blue-200 outline-none">
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded bg-white bg-opacity-20 border-transparent text-blue-500">
                            <span class="ml-2 text-blue-100">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-blue-100 hover:text-white underline">Forgot?</a>
                    </div>

                    <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-xl transition duration-200">
                        SIGN IN
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-white border-opacity-10 text-center">
                    <a href="{{ route('register') }}" class="text-sm font-bold text-white hover:underline">Create Account</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        .logo-bounce {
            animation: mini-bounce 3s infinite ease-in-out;
        }
        @keyframes mini-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</x-guest-layout>
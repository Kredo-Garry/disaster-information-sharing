<x-guest-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center relative fixed inset-0" 
         style="background-image: url('https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&q=80&w=1974');">
        
        <div class="absolute inset-0 bg-black bg-opacity-40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md mx-auto px-6 py-10">
            <div class="bg-white bg-opacity-10 backdrop-blur-xl border border-white border-opacity-20 rounded-[4.5rem] shadow-2xl p-10 text-white max-h-[85vh] overflow-y-auto custom-scrollbar">
                
                <div class="text-center mb-8">
                    <div class="flex justify-center mb-4 logo-bounce">
                        <svg width="100" height="100" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <h2 class="text-4xl font-bold tracking-tight">Create Account</h2>
                    <p class="text-blue-100 opacity-60 text-sm mt-2">Join Disaster Info Sharing</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="max-w-[85%] mx-auto space-y-4">
                        <div class="flex flex-col space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="w-full px-4 py-2 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 outline-none text-sm">
                        </div>

                        <div class="flex flex-col space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                   class="w-full px-4 py-2 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 outline-none text-sm">
                        </div>

                        <div class="flex flex-col space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Account Name</label>
                            <input id="account_name" type="text" name="account_name" value="{{ old('account_name') }}" required
                            autocomplete="off"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 outline-none text-sm">
                        </div>

                        <div class="flex flex-col space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Phone Number</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full px-4 py-2 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 outline-none text-sm">
                        </div>

                        <div class="flex flex-col space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Birth Date</label>
                            <input id="birth_date" type="text" name="birth_date" placeholder="Select Date"
                                   class="w-full px-4 py-2 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 outline-none text-sm">
                        </div>

                        <div class="flex flex-col space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Password</label>
                            <input id="password" type="password" name="password" required
                            autocomplete="new-password"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 outline-none text-sm">
                        </div>

                        <div class="flex flex-col space-y-1">
                            <label class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Confirm</label>
                           <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password"
                            class="w-full px-4 py-2 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 outline-none text-sm">
                        </div>

                        <div class="h-8"></div> 

                        <div> 
                            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-lg shadow-xl transition-all duration-300 uppercase tracking-[0.2em]">
                                Register
                            </button>
                        </div>
                    </div>

                    <div class="text-center pt-8 pb-2">
                        <a class="text-xs text-white/50 hover:text-white transition group" href="{{ route('login') }}">
                            Already registered? <span class="text-white font-bold underline underline-offset-4 decoration-blue-500/50">Sign In</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* バウンスアニメーションの追加 */
        .logo-bounce {
            animation: mini-bounce 3s infinite ease-in-out;
        }
        @keyframes mini-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        /* スクロールバーのカスタマイズ */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }
    </style>

    <script>
        flatpickr("#birth_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            locale: "en"
        });
    </script>
</x-guest-layout>
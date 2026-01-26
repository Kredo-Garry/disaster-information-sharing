<x-guest-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center relative fixed inset-0" 
         style="background-image: url('https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&q=80&w=1974');">
        
        <div class="absolute inset-0 bg-black bg-opacity-40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md mx-auto px-6">
            <div class="bg-white bg-opacity-10 backdrop-blur-xl border border-white border-opacity-20 rounded-[4.5rem] shadow-2xl p-10 md:p-14 text-white">
                
                <div class="text-center mb-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-6 shadow-lg shadow-blue-500/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-4xl font-bold tracking-tight">Create Account</h2>
                    <p class="text-blue-100 opacity-60 text-sm mt-2">Join Disaster Info Sharing</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <div class="flex flex-col space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 placeholder-gray-400 outline-none transition duration-200">
                        @error('name') <p class="mt-1 text-red-300 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                               class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 placeholder-gray-400 outline-none transition duration-200">
                        @error('email') <p class="mt-1 text-red-300 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 placeholder-gray-400 outline-none transition duration-200">
                        @error('password') <p class="mt-1 text-red-300 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col space-y-1">
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-blue-100 opacity-70 ml-2">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full px-5 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 text-gray-900 placeholder-gray-400 outline-none transition duration-200">
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 mt-4 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl font-bold text-xl shadow-xl transition-all duration-300 uppercase tracking-[0.2em]">
                            Register
                        </button>
                    </div>

                    <div class="text-center pt-4">
                        <a class="text-sm text-white/50 hover:text-white transition group" href="{{ route('login') }}">
                            <span class="inline-block mr-3">Already registered?</span> 
                            <span class="text-white font-bold underline underline-offset-8 decoration-blue-500/50">Sign In</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
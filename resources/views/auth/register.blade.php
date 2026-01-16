<x-guest-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center relative fixed inset-0" 
         style="background-image: url('https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&q=80&w=1974');">
        
        <div class="absolute inset-0 bg-black bg-opacity-40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md mx-auto px-6 py-8">
            
            <div class="bg-white bg-opacity-10 backdrop-blur-xl border border-white border-opacity-20 rounded-[2.5rem] shadow-2xl p-8 md:p-10 text-white">
                
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold tracking-tight">Create Account</h2>
                    <p class="text-blue-100 opacity-80 text-sm mt-2">Join Disaster Info Sharing</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-blue-100 mb-1 ml-1">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               class="w-full px-5 py-3 bg-white bg-opacity-10 border border-white border-opacity-30 rounded-xl focus:ring-4 focus:ring-blue-500 text-white placeholder-blue-200 outline-none transition duration-200">
                        @error('name') <p class="mt-1 text-red-300 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-blue-100 mb-1 ml-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                               class="w-full px-5 py-3 bg-white bg-opacity-10 border border-white border-opacity-30 rounded-xl focus:ring-4 focus:ring-blue-500 text-white placeholder-blue-200 outline-none transition duration-200">
                        @error('email') <p class="mt-1 text-red-300 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-blue-100 mb-1 ml-1">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full px-5 py-3 bg-white bg-opacity-10 border border-white border-opacity-30 rounded-xl focus:ring-4 focus:ring-blue-500 text-white placeholder-blue-200 outline-none transition duration-200">
                        @error('password') <p class="mt-1 text-red-300 text-xs font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-blue-100 mb-1 ml-1">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full px-5 py-3 bg-white bg-opacity-10 border border-white border-opacity-30 rounded-xl focus:ring-4 focus:ring-blue-500 text-white placeholder-blue-200 outline-none transition duration-200">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold shadow-xl transition duration-200 uppercase tracking-widest">
                            Register
                        </button>
                    </div>

                    <div class="text-center mt-6">
                        <a class="text-sm text-blue-100 hover:text-white underline underline-offset-4 transition" href="{{ route('login') }}">
                            Already registered? Sign In
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
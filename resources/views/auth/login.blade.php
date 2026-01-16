<x-guest-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center relative fixed inset-0" 
         style="background-image: url('https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&q=80&w=1974');">
        
        <div class="absolute inset-0 bg-black bg-opacity-40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md mx-auto px-6">
            
            <div class="bg-white bg-opacity-10 backdrop-blur-xl border border-white border-opacity-20 rounded-[2.5rem] shadow-2xl p-8 md:p-10 text-white">
                
                <div class="flex justify-center mb-6">
                    <div class="bg-blue-600 p-4 rounded-2xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A10.003 10.003 0 0012 3v8h8c0-1.547-.35-3.013-1.01-4.32a9.992 9.992 0 00-4.67-4.67C13.013 1.35 11.547 1 10 1s-3.013.35-4.32 1.01c-1.307.66-2.415 1.574-3.264 2.658A9.954 9.954 0 001 10c0 1.547.35 3.013 1.01 4.32a9.993 9.993 0 004.67 4.67c1.307.66 2.773 1.01 4.32 1.01v-8H2z"></path>
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
</x-guest-layout>
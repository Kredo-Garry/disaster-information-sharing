<x-guest-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div class="min-h-screen w-full flex items-center justify-center bg-cover bg-center relative fixed inset-0" 
         style="background-image: url('https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&q=80&w=1974');">
        
        <div class="absolute inset-0 bg-black bg-opacity-40 backdrop-blur-sm"></div>

        <div class="relative w-full max-w-md mx-auto px-6 py-10">
            <div class="bg-white bg-opacity-10 backdrop-blur-xl border border-white border-opacity-20 rounded-[4.5rem] shadow-2xl p-10 text-white max-h-[85vh] overflow-y-auto custom-scrollbar">
                
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4 shadow-lg shadow-blue-500/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
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

    <script>
        flatpickr("#birth_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "F j, Y",
            locale: "en"
        });
    </script>
    <p>sample</p>
</x-guest-layout>
@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Create New User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-bold">← Back to List</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Real Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                {{-- Account Name --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Account Name (@)</label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                {{-- Email --}}
                <div class="flex flex-col space-y-1 md:col-span-2">
                    <label class="text-xs font-bold text-gray-500 uppercase">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                {{-- Phone --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                {{-- Birth Date --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Birth Date</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                {{-- Password --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                {{-- Password Confirmation --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold transition shadow-md">
                    Create User Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
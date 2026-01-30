@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit User</h1>
        <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-bold">← Back to List</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT') {{-- 更新はPUTメソッドを使うにょ！ --}}
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Real Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-gray-700">
                </div>

                {{-- Account Name --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Account Name (@)</label>
                    <input type="text" name="account_name" value="{{ old('account_name', $user->account_name) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-gray-700">
                </div>

                {{-- Email --}}
                <div class="flex flex-col space-y-1 md:col-span-2">
                    <label class="text-xs font-bold text-gray-500 uppercase">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-gray-700">
                </div>

                {{-- Phone --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-gray-700">
                </div>

                {{-- Birth Date --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Birth Date</label>
                    {{-- date型は Y-m-d 形式で入れないと表示されないことがあるにょ --}}
                    <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-gray-700">
                </div>

                {{-- Password (任意) --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">New Password (Optional)</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-gray-700">
                </div>

                {{-- Password Confirmation --}}
                <div class="flex flex-col space-y-1">
                    <label class="text-xs font-bold text-gray-500 uppercase">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-gray-700">
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold transition shadow-md">
                    Update User Profile
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
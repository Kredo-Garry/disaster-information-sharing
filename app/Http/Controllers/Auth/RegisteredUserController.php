<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'account_name' => ['required', 'string', 'max:255'], // ✅ 追加
            'phone' => ['nullable', 'string', 'max:20'],       // ✅ 追加
            'birth_date' => ['nullable', 'date'],             // ✅ 追加
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'account_name' => $request->account_name, // ✅ 追加
            'phone' => $request->phone,               // ✅ 追加
            'birth_date' => $request->birth_date,     // ✅ 追加
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        if ($user->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->away('http://localhost:3000/home');
    }
    
}

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
            'role' => ['required', 'string', 'in:patient,doctor,hospital'],
            'first_name' => ['nullable', 'string', 'max:255', 'required_if:role,patient,doctor'],
            'last_name' => ['nullable', 'string', 'max:255', 'required_if:role,patient,doctor'],
            'nid' => ['nullable', 'string', 'max:255', 'unique:' . User::class, 'exists:valid_nids,nid_number', 'required_if:role,patient,doctor'],
            'age' => ['nullable', 'integer', 'min:0', 'required_if:role,patient,doctor'],
            'doctor_id' => ['nullable', 'string', 'max:255', 'unique:' . User::class, 'required_if:role,doctor'],
            'hospital_name' => ['nullable', 'string', 'max:255', 'required_if:role,hospital'],
            'dghs_number' => ['nullable', 'string', 'max:255', 'unique:' . User::class, 'required_if:role,hospital'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'nid' => $request->nid,
            'age' => $request->age,
            'role' => $request->role,
            'doctor_id' => $request->doctor_id,
            'hospital_name' => $request->hospital_name,
            'dghs_number' => $request->dghs_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Patient;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'customer',
            'country_code' => $request->country_code,
            'whatsapp_number' => $request->whatsapp_number,
            'otp_code' => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Create patient record linked to user
        Patient::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'country_code' => $user->country_code,
            'whatsapp_number' => $user->whatsapp_number,
        ]);

        Mail::to($user->email)->send(new OtpMail($otpCode));

        Auth::login($user);

        return redirect()->route('otp.verify')
            ->with('success', 'Registration successful! Please verify your email.');
    }
}

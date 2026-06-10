<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OtpController extends Controller
{
    public function showVerifyForm()
    {
        if (auth()->user()->email_verified_at) {
            return redirect()->route('dashboard.redirect');
        }

        return view('auth.otp-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $user = auth()->user();

        if (!$user->otp_code || $user->otp_code !== $request->otp_code) {
            return back()->withErrors(['otp_code' => 'Invalid OTP code.']);
        }

        if ($user->otp_expires_at && Carbon::now()->gt($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'OTP has expired. Please request a new one.']);
        }

        $user->update([
            'email_verified_at' => Carbon::now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        $redirect = $user->isCustomer() ? '/customer/dashboard' : '/admin/dashboard';

        return redirect($redirect)->with('success', 'Email verified successfully!');
    }

    public function resend()
    {
        $user = auth()->user();
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new OtpMail($otpCode));

        return back()->with('success', 'A new OTP has been sent to your email.');
    }
}

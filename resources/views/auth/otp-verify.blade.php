@extends('layouts.guest')
@section('title', 'Verify Email')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-2">Verify Your Email</h2>
<p class="text-gray-500 text-sm mb-6">We sent a 6-digit code to <strong>{{ auth()->user()->email }}</strong></p>

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('otp.verify.submit') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">OTP Code</label>
        <input type="text" name="otp_code" maxlength="6" required autofocus placeholder="000000"
               class="w-full px-4 py-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-2xl tracking-[0.5em] font-mono">
    </div>
    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
        Verify
    </button>
</form>

<div class="mt-4 text-center">
    <form method="POST" action="{{ route('otp.resend') }}">
        @csrf
        <button type="submit" class="text-sm text-blue-600 hover:underline">Resend OTP Code</button>
    </form>
</div>

<div class="mt-4 text-center">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="text-sm text-gray-500 hover:underline">Sign out</button>
    </form>
</div>
@endsection

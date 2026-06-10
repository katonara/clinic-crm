@extends('layouts.guest')
@section('title', 'Forgot Password')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-2">Forgot Password</h2>
<p class="text-gray-500 text-sm mb-6">Enter your email to receive a password reset link.</p>

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
        Send Reset Link
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-500">
    <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Back to Login</a>
</p>
@endsection

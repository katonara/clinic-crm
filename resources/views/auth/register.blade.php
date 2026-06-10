@extends('layouts.guest')
@section('title', 'Register')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">Create Account</h2>

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
    <ul class="list-disc list-inside">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input type="password" name="password" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input type="password" name="password_confirmation" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
        <div class="flex space-x-2">
            <select name="country_code" class="w-28 px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="+60" {{ old('country_code', '+60') == '+60' ? 'selected' : '' }}>+60 MY</option>
                <option value="+65" {{ old('country_code') == '+65' ? 'selected' : '' }}>+65 SG</option>
                <option value="+62" {{ old('country_code') == '+62' ? 'selected' : '' }}>+62 ID</option>
                <option value="+66" {{ old('country_code') == '+66' ? 'selected' : '' }}>+66 TH</option>
                <option value="+63" {{ old('country_code') == '+63' ? 'selected' : '' }}>+63 PH</option>
                <option value="+91" {{ old('country_code') == '+91' ? 'selected' : '' }}>+91 IN</option>
                <option value="+44" {{ old('country_code') == '+44' ? 'selected' : '' }}>+44 UK</option>
                <option value="+1" {{ old('country_code') == '+1' ? 'selected' : '' }}>+1 US</option>
                <option value="+61" {{ old('country_code') == '+61' ? 'selected' : '' }}>+61 AU</option>
                <option value="+81" {{ old('country_code') == '+81' ? 'selected' : '' }}>+81 JP</option>
                <option value="+86" {{ old('country_code') == '+86' ? 'selected' : '' }}>+86 CN</option>
                <option value="+971" {{ old('country_code') == '+971' ? 'selected' : '' }}>+971 AE</option>
            </select>
            <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number') }}" required placeholder="12 345 6789"
                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>
    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
        Register
    </button>
</form>

<p class="mt-6 text-center text-sm text-gray-500">
    Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Sign In</a>
</p>
@endsection

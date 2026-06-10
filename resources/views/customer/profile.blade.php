@extends('layouts.customer')
@section('title', 'My Profile')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">My Profile</h1>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    @if(session('success'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-500">
            <p class="text-xs text-gray-400 mt-1">Email cannot be changed.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
            <div class="flex space-x-2">
                <select name="country_code" class="w-28 px-3 py-3 border border-gray-300 rounded-lg text-sm">
                    @foreach(['+60','+65','+62','+1','+44'] as $cc)
                    <option value="{{ $cc }}" {{ old('country_code', $user->country_code) == $cc ? 'selected' : '' }}>{{ $cc }}</option>
                    @endforeach
                </select>
                <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $user->whatsapp_number) }}" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg" placeholder="1234567890">
            </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">Update Profile</button>
    </form>
</div>

<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Account</h2>
    <div class="text-sm text-gray-500 space-y-2">
        <p><span class="font-medium text-gray-700">Member since:</span> {{ $user->created_at->format('d M Y') }}</p>
        <p><span class="font-medium text-gray-700">Email verified:</span> {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y') : 'Not verified' }}</p>
    </div>
</div>
@endsection

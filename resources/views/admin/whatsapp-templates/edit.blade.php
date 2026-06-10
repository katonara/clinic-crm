@extends('layouts.app')
@section('title', 'Edit WhatsApp Template')
@section('page-title', 'Edit WhatsApp Template')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.whatsapp-templates.update', $template) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" name="title" value="{{ old('title', $template->title) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        @foreach(['booking_confirmation','booking_reminder','follow_up','reschedule','payment_reminder'] as $t)
                        <option value="{{ $t }}" {{ $template->type === $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="active" {{ $template->status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $template->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
                <textarea name="message" rows="6" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('message', $template->message) }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Placeholders: {patient_name} {service_name} {booking_date} {booking_time} {clinic_name} {clinic_whatsapp}</p>
            </div>
            <div class="flex space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">Update</button>
                <a href="{{ route('admin.whatsapp-templates.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

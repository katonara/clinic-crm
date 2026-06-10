@extends('layouts.app')
@section('title', 'Patients')
@section('page-title', 'Patients')
@section('header-actions')
<a href="{{ route('admin.patients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Patient</a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-4 border-b border-gray-100">
        <form method="GET" class="flex">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or WhatsApp..."
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <button type="submit" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">Search</button>
        </form>
    </div>

    <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WhatsApp</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($patients as $patient)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $patient->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->email ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $patient->country_code }}{{ $patient->whatsapp_number }}</td>
                    <td class="px-6 py-4 text-sm">@if($patient->tag)<x-badge color="blue">{{ $patient->tag }}</x-badge>@else - @endif</td>
                    <td class="px-6 py-4 text-sm space-x-2">
                        <a href="{{ route('admin.patients.show', $patient) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('admin.patients.edit', $patient) }}" class="text-gray-600 hover:underline">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5"><x-empty-state message="No patients found." action="Add Patient" :actionUrl="route('admin.patients.create')" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="md:hidden divide-y divide-gray-100">
        @forelse($patients as $patient)
        <a href="{{ route('admin.patients.show', $patient) }}" class="block p-4 hover:bg-gray-50">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-medium text-gray-800">{{ $patient->name }}</p>
                    <p class="text-sm text-gray-500">{{ $patient->country_code }}{{ $patient->whatsapp_number }}</p>
                </div>
                @if($patient->tag)<x-badge color="blue">{{ $patient->tag }}</x-badge>@endif
            </div>
        </a>
        @empty
        <x-empty-state message="No patients found." action="Add Patient" :actionUrl="route('admin.patients.create')" />
        @endforelse
    </div>

    <div class="p-4">{{ $patients->links() }}</div>
</div>
@endsection

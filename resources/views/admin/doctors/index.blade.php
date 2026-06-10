@extends('layouts.app')
@section('title', 'Doctors')
@section('page-title', 'Doctors')
@section('header-actions')
<a href="{{ route('admin.doctors.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Doctor</a>
@endsection

@section('content')
{{-- Desktop table --}}
<div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Specialty</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WhatsApp</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($doctors as $doctor)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $doctor->user->name }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor->specialty ?? '-' }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor->user->email }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ $doctor->user->country_code }}{{ $doctor->user->whatsapp_number }}</td>
                <td class="px-6 py-4"><x-badge :color="$doctor->status === 'active' ? 'green' : 'gray'">{{ ucfirst($doctor->status) }}</x-badge></td>
                <td class="px-6 py-4 text-right space-x-2">
                    <a href="{{ route('admin.doctors.show', $doctor) }}" class="text-blue-600 hover:underline text-sm">View</a>
                    <a href="{{ route('admin.doctors.slots', $doctor) }}" class="text-purple-600 hover:underline text-sm">Slots</a>
                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="text-yellow-600 hover:underline text-sm">Edit</a>
                    <form method="POST" action="{{ route('admin.doctors.destroy', $doctor) }}" class="inline" x-data
                          @submit.prevent="if(confirm('Delete this doctor?')) $el.submit()">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6"><x-empty-state message="No doctors found." action="+ Add Doctor" :actionUrl="route('admin.doctors.create')" /></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Mobile cards --}}
<div class="md:hidden space-y-3">
    @forelse($doctors as $doctor)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex justify-between items-start mb-3">
            <div>
                <p class="font-semibold text-gray-800">{{ $doctor->user->name }}</p>
                <p class="text-sm text-gray-500">{{ $doctor->specialty ?? 'No specialty' }}</p>
            </div>
            <x-badge :color="$doctor->status === 'active' ? 'green' : 'gray'">{{ ucfirst($doctor->status) }}</x-badge>
        </div>
        <p class="text-sm text-gray-600 mb-1">{{ $doctor->user->email }}</p>
        <p class="text-sm text-gray-600 mb-3">{{ $doctor->user->country_code }}{{ $doctor->user->whatsapp_number }}</p>
        <div class="flex space-x-2">
            <a href="{{ route('admin.doctors.show', $doctor) }}" class="flex-1 text-center bg-blue-50 text-blue-700 py-2 rounded-lg text-sm font-medium">View</a>
            <a href="{{ route('admin.doctors.slots', $doctor) }}" class="flex-1 text-center bg-purple-50 text-purple-700 py-2 rounded-lg text-sm font-medium">Slots</a>
            <a href="{{ route('admin.doctors.edit', $doctor) }}" class="flex-1 text-center bg-yellow-50 text-yellow-700 py-2 rounded-lg text-sm font-medium">Edit</a>
        </div>
    </div>
    @empty
    <x-empty-state message="No doctors found." action="+ Add Doctor" :actionUrl="route('admin.doctors.create')" />
    @endforelse
</div>

<div class="mt-4">{{ $doctors->links() }}</div>
@endsection

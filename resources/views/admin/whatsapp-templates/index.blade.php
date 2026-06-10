@extends('layouts.app')
@section('title', 'WhatsApp Templates')
@section('page-title', 'WhatsApp Templates')
@section('header-actions')
<a href="{{ route('admin.whatsapp-templates.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">+ Add Template</a>
@endsection

@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($templates as $template)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex justify-between items-start mb-3">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $template->title }}</h3>
                <x-badge color="blue">{{ ucfirst(str_replace('_',' ',$template->type)) }}</x-badge>
            </div>
            <x-badge :color="$template->status === 'active' ? 'green' : 'gray'">{{ ucfirst($template->status) }}</x-badge>
        </div>
        <p class="text-sm text-gray-500 mb-3 whitespace-pre-line">{{ Str::limit($template->message, 120) }}</p>
        <a href="{{ route('admin.whatsapp-templates.edit', $template) }}" class="text-blue-600 hover:underline text-sm">Edit</a>
    </div>
    @empty
    <div class="col-span-full"><x-empty-state message="No templates." action="Add Template" :actionUrl="route('admin.whatsapp-templates.create')" /></div>
    @endforelse
</div>
<div class="mt-4">{{ $templates->links() }}</div>
@endsection

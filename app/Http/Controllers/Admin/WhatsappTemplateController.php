<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\WhatsappTemplateRequest;
use App\Models\WhatsappTemplate;

class WhatsappTemplateController extends Controller
{
    public function index()
    {
        $templates = WhatsappTemplate::orderBy('type')->paginate(15);

        return view('admin.whatsapp-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.whatsapp-templates.create');
    }

    public function store(WhatsappTemplateRequest $request)
    {
        WhatsappTemplate::create($request->validated());

        return redirect()->route('admin.whatsapp-templates.index')
            ->with('success', 'Template created successfully.');
    }

    public function edit(WhatsappTemplate $whatsappTemplate)
    {
        return view('admin.whatsapp-templates.edit', ['template' => $whatsappTemplate]);
    }

    public function update(WhatsappTemplateRequest $request, WhatsappTemplate $whatsappTemplate)
    {
        $whatsappTemplate->update($request->validated());

        return redirect()->route('admin.whatsapp-templates.index')
            ->with('success', 'Template updated successfully.');
    }

    public function destroy(WhatsappTemplate $whatsappTemplate)
    {
        $whatsappTemplate->delete();

        return redirect()->route('admin.whatsapp-templates.index')
            ->with('success', 'Template deleted successfully.');
    }
}

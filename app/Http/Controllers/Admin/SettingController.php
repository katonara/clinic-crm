<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClinicSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = ClinicSetting::instance();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'whatsapp_country_code' => 'required|string|max:10',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'default_slot_duration' => 'required|integer|min:5|max:120',
            'opening_time' => 'required|date_format:H:i',
            'closing_time' => 'required|date_format:H:i',
            'logo' => 'nullable|image|max:2048',
        ]);

        $settings = ClinicSetting::instance();
        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $settings->update($data);

        return back()->with('success', 'Settings updated successfully.');
    }
}

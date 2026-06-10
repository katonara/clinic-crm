<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        return view('customer.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'whatsapp_number' => 'required|string|max:20',
        ]);

        $user = auth()->user();
        $user->update($request->only('name', 'country_code', 'whatsapp_number'));

        if ($patient = $user->patient) {
            $patient->update([
                'name' => $request->name,
                'country_code' => $request->country_code,
                'whatsapp_number' => $request->whatsapp_number,
            ]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }
}

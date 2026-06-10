<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StaffRequest;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\Service;

class StaffController extends Controller
{
    public function index()
    {
        $staff = User::whereIn('role', ['staff', 'doctor'])
            ->with('staffProfile')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        $services = Service::active()->get();

        return view('admin.staff.create', compact('services'));
    }

    public function store(StaffRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role,
            'country_code' => $request->country_code,
            'whatsapp_number' => $request->whatsapp_number,
            'status' => $request->status,
            'email_verified_at' => now(),
        ]);

        StaffProfile::create([
            'user_id' => $user->id,
            'position' => $request->position,
            'bio' => $request->bio,
            'status' => $request->status,
        ]);

        if ($request->has('service_ids')) {
            $user->services()->sync($request->service_ids);
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member created successfully.');
    }

    public function edit(User $staff)
    {
        $services = Service::active()->get();
        $staff->load('staffProfile', 'services');

        return view('admin.staff.edit', compact('staff', 'services'));
    }

    public function update(StaffRequest $request, User $staff)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'country_code' => $request->country_code,
            'whatsapp_number' => $request->whatsapp_number,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $staff->update($data);

        $staff->staffProfile()->updateOrCreate(
            ['user_id' => $staff->id],
            [
                'position' => $request->position,
                'bio' => $request->bio,
                'status' => $request->status,
            ]
        );

        if ($request->has('service_ids')) {
            $staff->services()->sync($request->service_ids);
        } else {
            $staff->services()->detach();
        }

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff)
    {
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }
}

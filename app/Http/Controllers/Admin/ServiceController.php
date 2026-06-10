<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Models\User;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::withCount('staff', 'bookings')->orderByDesc('created_at')->paginate(15);

        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->where('status', 'active')->get();

        return view('admin.services.create', compact('staffMembers'));
    }

    public function store(ServiceRequest $request)
    {
        $service = Service::create($request->except('staff_ids'));

        if ($request->has('staff_ids')) {
            $service->staff()->sync($request->staff_ids);
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function edit(Service $service)
    {
        $staffMembers = User::whereIn('role', ['staff', 'doctor'])->where('status', 'active')->get();
        $service->load('staff');

        return view('admin.services.edit', compact('service', 'staffMembers'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $service->update($request->except('staff_ids'));

        if ($request->has('staff_ids')) {
            $service->staff()->sync($request->staff_ids);
        } else {
            $service->staff()->detach();
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}

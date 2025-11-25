<?php

namespace App\Http\Controllers;

use App\Models\VehicleUsage;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleUsageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function userHasRole($roles)
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Manual check against the roles relationship to avoid calling undefined methods
        foreach ((array)$roles as $role) {
            if ($user->roles->contains('name', $role)) {
                return true;
            }
        }

        return false;
    }

    public function index(Request $request)
    {
        $query = VehicleUsage::with(['vehicle', 'user', 'approver']);

        if (!$this->userHasRole(['super admin', 'admin'])) {
            $query->where('user_id', Auth::id());
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%$search%")
                    ->orWhereHas('vehicle', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('license_plate', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $usages = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('vehicle-usage.index', compact('usages'));
    }

    public function create()
    {
        $vehicles = Vehicle::where('is_available', true)->get();
        return view('vehicle-usage.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'purpose' => 'required|string|max:500',
        ]);

        VehicleUsage::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $request->vehicle_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('vehicle-usage.index')->with('success', 'Vehicle usage request submitted successfully.');
    }

    public function show(VehicleUsage $vehicleUsage)
    {
        if (!$this->userHasRole(['super admin', 'admin']) && $vehicleUsage->user_id != Auth::id()) {
            abort(403);
        }

        return view('vehicle-usage.show', compact('vehicleUsage'));
    }

    public function approve(VehicleUsage $vehicleUsage)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        if ($vehicleUsage->status != 'pending') {
            return redirect()->route('vehicle-usage.index')
                ->with('error', 'Request has already been processed.');
        }

        $vehicleUsage->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        $vehicle = $vehicleUsage->vehicle;
        $vehicle->is_available = false;
        $vehicle->save();

        return redirect()->route('vehicle-usage.index')->with('success', 'Vehicle usage approved successfully.');
    }

    public function reject(Request $request, VehicleUsage $vehicleUsage)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($vehicleUsage->status != 'pending') {
            return redirect()->route('vehicle-usage.index')
                ->with('error', 'Request has already been processed.');
        }

        $vehicleUsage->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('vehicle-usage.index')->with('success', 'Vehicle usage rejected successfully.');
    }

    public function return(VehicleUsage $vehicleUsage)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        if ($vehicleUsage->status == 'returned') {
            return redirect()->route('vehicle-usage.index')
                ->with('error', 'Vehicle has already been returned.');
        }

        $vehicleUsage->update([
            'status' => 'returned',
        ]);

        $vehicle = $vehicleUsage->vehicle;
        $vehicle->is_available = true;
        $vehicle->save();

        return redirect()->route('vehicle-usage.index')->with('success', 'Vehicle returned successfully.');
    }

    public function destroy(VehicleUsage $vehicleUsage)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        if (in_array($vehicleUsage->status, ['approved', 'returned'])) {
            return redirect()->route('vehicle-usage.index')
                ->with('error', 'Cannot delete a processed vehicle usage record.');
        }

        $vehicleUsage->delete();
        return redirect()->route('vehicle-usage.index')->with('success', 'Vehicle usage record deleted successfully.');
    }
}

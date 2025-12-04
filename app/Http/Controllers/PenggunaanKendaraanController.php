<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\PenggunaanKendaraan;
use App\Models\VehicleUsage;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenggunaanKendaraanController extends Controller
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
        $query = PenggunaanKendaraan::with(['vehicle', 'user', 'approver']);

        if (!$this->userHasRole(['super admin', 'admin'])) {
            $query->where('user_id', Auth::id());
        }

        // Manual search implementation
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tujuan', 'like', "%$search%")
                    ->orWhereHas('kendaraan', function ($q) use ($search) {
                        $q->where('nama', 'like', "%$search%")
                            ->orWhere('nomor_polisi', 'like', "%$search%");
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
        return view('penggunaan-kendaraan.index', compact('usages'));
    }

    public function create()
    {
        $kendaraan = Kendaraan::where('tersedia', true)->get();
        return view('penggunaan-kendaraan.create', compact('kendaraan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraans,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tujuan' => 'required|string|max:500',
        ]);

        PenggunaanKendaraan::create([
            'user_id' => Auth::id(),
            'kendaraan_id' => $request->kendaraan_id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tujuan' => $request->tujuan,
        ]);

        return redirect()->route('penggunaan-kendaraan.index')->with('success', 'Vehicle usage request submitted successfully.');
    }

    public function show(PenggunaanKendaraan $penggunaanKendaraan)
    {
        if (!$this->userHasRole(['super admin', 'admin']) && $penggunaanKendaraan->user_id != Auth::id()) {
            abort(403);
        }

        return view('penggunaan-kendaraan.show', compact('penggunaanKendaraan'));
    }

    public function approve(PenggunaanKendaraan $penggunaanKendaraan)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        if ($penggunaanKendaraan->status != 'pending') {
            return redirect()->route('penggunaan-kendaraan.index')
                ->with('error', 'Request has already been processed.');
        }

        $penggunaanKendaraan->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        $vehicle = $penggunaanKendaraan->kendaraan;
        $vehicle->is_available = false;
        $vehicle->save();

        return redirect()->route('penggunaan-kendaraan.index')->with('success', 'Vehicle usage approved successfully.');
    }

    public function reject(Request $request, PenggunaanKendaraan $penggunaanKendaraan)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($penggunaanKendaraan->status != 'pending') {
            return redirect()->route('penggunaan-kendaraan.index')
                ->with('error', 'Request has already been processed.');
        }

        $penggunaanKendaraan->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('penggunaan-kendaraan.index')->with('success', 'Vehicle usage rejected successfully.');
    }

    public function return(PenggunaanKendaraan $penggunaanKendaraan)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        if ($penggunaanKendaraan->status == 'returned') {
            return redirect()->route('penggunaan-kendaraan.index')
                ->with('error', 'Vehicle has already been returned.');
        }

        $penggunaanKendaraan->update([
            'status' => 'returned',
        ]);

        $vehicle = $penggunaanKendaraan->kendaraan;
        $vehicle->is_available = true;
        $vehicle->save();

        return redirect()->route('penggunaan-kendaraan.index')->with('success', 'Vehicle returned successfully.');
    }

    public function destroy(PenggunaanKendaraan $penggunaanKendaraan)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        if (in_array($penggunaanKendaraan->status, ['approved', 'returned'])) {
            return redirect()->route('penggunaan-kendaraan.index')
                ->with('error', 'Cannot delete a processed vehicle usage record.');
        }

        $penggunaanKendaraan->delete();
        return redirect()->route('penggunaan-kendaraan.index')->with('success', 'Vehicle usage record deleted successfully.');
    }
}

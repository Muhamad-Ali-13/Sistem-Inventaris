<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KendaraanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Manual permission check - alternatif tanpa middleware
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            // Safe role check
            if ($this->userHasRole($user, 'super admin')) {
                return $next($request);
            }

            if ($this->userHasRole($user, 'admin')) {
                return $next($request);
            }

            // Karyawan tidak bisa akses
            abort(403, 'Unauthorized action.');
        });
    }

    /**
     * Safe method to check user role
     */
    private function userHasRole($user, $role)
    {
        // Method 1: Using hasRole if method exists
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        // Method 2: Manual check through roles
        foreach ($user->roles as $userRole) {
            if ($userRole->name === $role) {
                return true;
            }
        }

        return false;
    }

    public function index(Request $request)
    {
        $query = Kendaraan::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama', 'like', "%$search%")
                ->orWhere('plat_nomor', 'like', "%$search%")
                ->orWhere('tipe', 'like', "%$search%");
        }

        if ($request->has('availability') && $request->availability != '') {
            $query->where('is_available', $request->availability);
        }

        $kendaraan = $query->paginate(10);
        return view('kendaraan.index', compact('kendaraan'));
    }

    public function create()
    {
        return view('kendaraan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:20|unique:kendaraan,plat_nomor',
            'tipe' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'konsumsi_bahan_bakar' => 'nullable|integer|min:0',
            'perawatan_terakhir' => 'nullable|date',
            'tersedia' => 'required|boolean',
        ]);

        Kendaraan::create($request->all());

        return redirect()->route('kendaraan.index')->with('success', 'Vehicle created successfully.');
    }

    public function show(Kendaraan $kendaraan)
    {
        return view('kendaraan.show', compact('kendaraan'));
    }

    public function edit(Kendaraan $kendaraan)
    {
        return view('kendaraan.edit', compact('kendaraan'));
    }

    public function update(Request $request, Kendaraan $kendaraan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'plat_nomor' => 'required|string|max:20|unique:kendaraan,plat_nomor,' . $kendaraan->id,
            'tipe' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'konsumsi_bahan_bakar' => 'nullable|integer|min:0',
            'perawatan_terakhir' => 'nullable|date',
            'tersedia' => 'required|boolean',
        ]);

        $kendaraan->update($request->all());

        return redirect()->route('kendaraan.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Kendaraan $kendaraan)
    {
        if ($kendaraan->vehicleUsage()->count() > 0) {
            return redirect()->route('kendaraan.index')
                ->with('error', 'Cannot delete vehicle. There are usage records associated with this vehicle.');
        }

        $kendaraan->delete();
        return redirect()->route('kendaraan.index')->with('success', 'Vehicle deleted successfully.');
    }
}

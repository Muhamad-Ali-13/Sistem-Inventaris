<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaryawanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            if ($this->userHasRole($user, 'super admin') || $this->userHasRole($user, 'admin')) {
                return $next($request);
            }

            abort(403, 'Unauthorized action.');
        });
    }

    public function index(Request $request)
    {
        $query = Karyawan::with('department');

        // Manual search implementation
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('position', 'like', "%$search%")
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        $karyawan = $query->paginate(10);
        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('karyawan.create', compact('departments'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:karyawan,email',
            'telepon' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'jabatan' => 'required|string|max:255', // Pastikan ini ada
        ]);


        Karyawan::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'telepon' => $request->telepon,
            'department_id' => $request->department_id,
            'jabatan' => $request->jabatan, // Pastikan ini ada
        ]);

        return redirect()->route('karyawan.index')->with('success', 'karyawan created successfully.');
    }

    public function show(Karyawan $karyawan)
    {
        return view('karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        $departments = Department::all();
        return view('karyawan.edit', compact('karyawan', 'departments'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:karyawan,email,' . $karyawan->id,
            'telepon' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'jabatan' => 'required|string|max:255',
        ]);

        $karyawan->update($request->all());

        return redirect()->route('karyawan.index')
            ->with('success', 'karyawan updated successfully.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();
        return redirect()->route('karyawans.index')
            ->with('success', 'karyawan deleted successfully.');
    }

    private function userHasRole($user, $role)
    {
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        foreach ($user->roles as $userRole) {
            if ($userRole->name === $role) {
                return true;
            }
        }

        return false;
    }
}

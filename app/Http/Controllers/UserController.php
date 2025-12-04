<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Karyawan;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            if ($this->userHasRole($user, 'super admin')) {
                return $next($request);
            }

            if ($this->userHasRole($user, 'admin')) {
                return $next($request);
            }

            abort(403, 'Unauthorized action.');
        });
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

    public function index(Request $request)
    {
        $query = User::with('roles', 'karyawan');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhereHas('karyawan', function ($q) use ($search) {
                    $q->where('nama', 'like', "%$search%");
                });
        }

        $users = $query->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        
        // Ambil karyawan yang belum memiliki user
        $karyawan = Karyawan::belumPunyaUser()->get();

        return view('users.create', compact('roles', 'karyawan'));
    }

    public function store(Request $request)
    {
        // Validasi dasar
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'karyawan_id' => 'nullable|exists:karyawan,id',
        ]);

        // Cek apakah role membutuhkan karyawan_id
        $selectedRoles = $request->roles;
        $karyawanId = $request->karyawan_id;
        
        // Role yang BOLEH tanpa karyawan_id
        $rolesWithoutKaryawan = ['super admin', 'admin'];
        
        // Role yang HARUS punya karyawan_id
        $rolesWithKaryawan = ['karyawan'];
        
        // Cek apakah ada role yang mengharuskan karyawan_id
        $requireKaryawan = false;
        foreach ($selectedRoles as $role) {
            if (in_array($role, $rolesWithKaryawan)) {
                $requireKaryawan = true;
                break;
            }
        }
        
        // Jika role mengharuskan karyawan_id tapi tidak dipilih
        if ($requireKaryawan && !$karyawanId) {
            return redirect()->back()
                ->with('error', 'Role "karyawan" membutuhkan data karyawan.')
                ->withInput();
        }
        
        // Jika memilih karyawan, cek apakah sudah punya user
        if ($karyawanId) {
            $karyawan = Karyawan::findOrFail($karyawanId);
            
            if ($karyawan->user) {
                return redirect()->back()
                    ->with('error', 'Karyawan ini sudah memiliki akun user.')
                    ->withInput();
            }
            
            // Untuk karyawan, pastikan email sama
            if ($karyawan->email !== $request->email) {
                return redirect()->back()
                    ->with('error', 'Email harus sama dengan email karyawan: ' . $karyawan->email)
                    ->withInput();
            }
        }

        // Buat user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'karyawan_id' => $karyawanId, // Bisa null untuk super admin/admin
        ]);

        // Assign roles
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        
        // Untuk edit, tampilkan juga karyawan yang belum punya user + karyawan yang sedang dipakai user ini
        $karyawan = Karyawan::belumPunyaUser()->get();
        if ($user->karyawan_id) {
            $karyawan = $karyawan->push(Karyawan::find($user->karyawan_id));
        }
        
        return view('users.edit', compact('user', 'roles', 'karyawan'));
    }

    public function update(Request $request, User $user)
    {
        // Validasi
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'karyawan_id' => 'nullable|exists:karyawan,id',
        ]);
        
        // Cek apakah role membutuhkan karyawan_id
        $selectedRoles = $request->roles;
        $karyawanId = $request->karyawan_id;
        
        $rolesWithoutKaryawan = ['super admin', 'admin'];
        $rolesWithKaryawan = ['karyawan'];
        
        $requireKaryawan = false;
        foreach ($selectedRoles as $role) {
            if (in_array($role, $rolesWithKaryawan)) {
                $requireKaryawan = true;
                break;
            }
        }
        
        // Jika role mengharuskan karyawan_id tapi tidak dipilih
        if ($requireKaryawan && !$karyawanId) {
            return redirect()->back()
                ->with('error', 'Role "karyawan" membutuhkan data karyawan.')
                ->withInput();
        }
        
        // Jika memilih karyawan, cek apakah sudah punya user lain
        if ($karyawanId && $karyawanId != $user->karyawan_id) {
            $karyawan = Karyawan::findOrFail($karyawanId);
            
            if ($karyawan->user && $karyawan->user->id != $user->id) {
                return redirect()->back()
                    ->with('error', 'Karyawan ini sudah memiliki akun user lain.')
                    ->withInput();
            }
        }

        // Update data user
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'karyawan_id' => $karyawanId,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->syncRoles($request->roles);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
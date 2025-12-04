<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
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
        $query = Kategori::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('nama', 'like', "%$search%")
                ->orWhere('deskripsi', 'like', "%$search%");
        }

        $kategori = $query->paginate(10);
        return view('kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama',
            'deskripsi' => 'nullable|string',
        ]);

        Kategori::create($request->all());

        return redirect()->route('kategori.index')->with('success', 'Category created successfully.');
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama,' . $kategori->id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update($request->all());

        return redirect()->route('kategori.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->items()->count() > 0) {
            return redirect()->route('kategori.index')
                ->with('error', 'Cannot delete category. There are items associated with this category.');
        }

        $kategori->delete();
        return redirect()->route('kategori.index')->with('success', 'Category deleted successfully.');
    }
}

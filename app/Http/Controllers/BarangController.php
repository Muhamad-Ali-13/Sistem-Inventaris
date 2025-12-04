<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BarangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Manual permission check
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
        $query = Barang::with('kategori');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                    ->orWhere('kode', 'like', "%$search%")
                    ->orWhere('deskripsi', 'like', "%$search%")
                    ->orWhere('lokasi', 'like', "%$search%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama', 'like', "%$search%");
                    });
            });
        }

        $barang = $query->paginate(10);
        $kategoris = Kategori::all(); // Tambahkan ini

        return view('barang.index', compact('barang', 'kategoris')); // Update compact
    }

    public function create(Request $request)
    {
        $categories = Kategori::all();
        $nama = $request->input('nama');
        if ($nama) {
            $generatedKode = $this->generateKodeBarang($nama);
        } else {
            // Handle the case where 'nama' is not provided
            $generatedKode = null;
        }
        return view('barang.create', compact('categories', 'generatedKode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_id' => 'required|exists:kategori,id',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'lokasi' => 'nullable|string|max:255',
        ]);

        try {
            // Generate kode berdasarkan nama barang
            $kode = $this->generateKodeBarang($request->input('nama'));

            $item = Barang::create([
                'nama' => $request->input('nama'),
                'kode' => $kode,
                'deskripsi' => $request->input('deskripsi'),
                'kategori_id' => $request->input('kategori_id'),
                'stok' => $request->input('stok'),
                'stok_minimal' => $request->input('stok_minimal'),
                'harga' => $request->input('harga'),
                'lokasi' => $request->input('lokasi'),
            ]);

            return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan dengan kode: ' . $kode);
        } catch (\Exception $e) {
            Log::error('Error creating barang: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menambahkan barang: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate kode barang berdasarkan nama.
     *
     * @param  string  $nama
     * @return string
     */
    private function generateKodeBarang(string $nama): string
    {

        // Pilihan 3: Format HP-001, LP-001, dst
        return $this->generateKodeOption2($nama);
    }

    /**
     * Pilihan 2: Ambil inisial tiap kata + nomor urut
     * Contoh: "HP Samsung" -> HS001, "Laptop Asus" -> LA001
     *
     * @param  string  $nama
     * @return string
     */
    private function generateKodeOption2(string $nama): string
    {
        // Pecah nama menjadi kata-kata
        $words = explode(' ', $nama);

        // Ambil huruf pertama dari 2 kata pertama (jika ada)
        if (count($words) >= 2) {
            $prefix = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        } else {
            // Jika hanya 1 kata, ambil 2 huruf pertama
            $prefix = strtoupper(substr($nama, 0, 2));
        }

        // Cari barang dengan prefix yang sama
        $lastBarang = Barang::where('kode', 'like', $prefix . '%')
            ->orderBy('kode', 'desc')
            ->first();

        if ($lastBarang) {
            $lastNumber = intval(substr($lastBarang->kode, 2));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }



    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Show the form for displaying the specified item.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    /*******  8b58d776-8046-4251-917e-cbd3b0514c04  *******/
    public function show(Barang $barang)
    {
        $categories = Kategori::all(); // Tambahkan ini
        return view('barang.show', compact('barang', 'categories')); // Update compact
    }

    public function edit(Barang $barang)
    {
        $categories = Kategori::all();
        return view('barang.edit', compact('barang', 'categories'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'kategori_id' => 'required|exists:kategoris,id',
            'stok' => 'required|integer|min:0',
            'stok_minimal' => 'required|integer|min:0',
            'lokasi' => 'nullable|string|max:255',
        ]);

        $barang->update($request->all());

        return redirect()->route('barang.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->transactions()->count() > 0 || $barang->itemRequests()->count() > 0) {
            return redirect()->route('barang.index')
                ->with('error', 'Cannot delete item. There are transactions or requests associated with this item.');
        }

        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Item deleted successfully.');
    }
}

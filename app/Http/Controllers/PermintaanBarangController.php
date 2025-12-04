<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermintaanBarangController extends Controller
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

            if ($this->userHasRole($user, 'karyawan')) {
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
        $query = PermintaanBarang::with(['barang', 'user', 'approver']);

        // Filter by user role
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin'])) {
            $query->where('user_id', Auth::id());
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tujuan', 'like', "%$search%")
                    ->orWhere('kode_permintaan', 'like', "%$search%")
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->where('nama', 'like', "%$search%")
                          ->orWhere('kode', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('permintaan-barang.index', compact('requests'));
    }

    public function create()
    {
        $barang = Barang::where('stok', '>', 0)->get();
        return view('permintaan-barang.create', compact('barang'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'barang_id' => 'required|exists:barang,id',
                'jumlah' => 'required|integer|min:1',
                'tujuan' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
            ]);

            // Cek stok barang
            $barang = Barang::findOrFail($request->barang_id);
            if ($barang->stok < $request->jumlah) {
                return redirect()->back()
                    ->withErrors(['jumlah' => 'Stok barang tidak mencukupi. Stok tersedia: ' . $barang->stok])
                    ->withInput();
            }

            // Generate kode permintaan
            $kodePermintaan = 'PRM-' . date('Ymd') . '-' . strtoupper(uniqid());

            // Hitung harga
            $hargaSatuan = $barang->harga;
            $totalHarga = $hargaSatuan * $request->jumlah;

            // Buat permintaan
            $permintaanBarang = PermintaanBarang::create([
                'user_id' => Auth::id(),
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHarga,
                'tujuan' => $request->tujuan,
                'keterangan' => $request->keterangan,
                'kode_permintaan' => $kodePermintaan,
                'status' => 'pending', // Default status
            ]);

            return redirect()->route('permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil dibuat. Menunggu persetujuan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(PermintaanBarang $permintaanBarang)
    {
        // Authorization check
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin']) && 
            $permintaanBarang->user_id != Auth::id()) {
            abort(403);
        }

        return view('permintaan-barang.show', compact('permintaanBarang'));
    }

    public function edit(PermintaanBarang $permintaanBarang)
    {
        // Authorization check
        if ($permintaanBarang->user_id != Auth::id()) {
            abort(403);
        }

        // Cek apakah bisa diedit (hanya status pending yang bisa diedit)
        if ($permintaanBarang->status != 'pending') {
            return redirect()->route('permintaan-barang.index')
                ->with('error', 'Permintaan sudah diproses, tidak dapat diubah.');
        }

        $barang = Barang::where('stok', '>', 0)->get();
        return view('permintaan-barang.edit', compact('permintaanBarang', 'barang'));
    }

    public function update(Request $request, PermintaanBarang $permintaanBarang)
    {
        // Authorization check
        if ($permintaanBarang->user_id != Auth::id()) {
            abort(403);
        }

        // Cek apakah bisa diupdate (hanya status pending yang bisa diupdate)
        if ($permintaanBarang->status != 'pending') {
            return redirect()->route('permintaan-barang.index')
                ->with('error', 'Permintaan sudah diproses, tidak dapat diubah.');
        }

        try {
            $request->validate([
                'barang_id' => 'required|exists:barang,id',
                'jumlah' => 'required|integer|min:1',
                'tujuan' => 'required|string|max:255',
                'keterangan' => 'nullable|string',
            ]);

            // Cek stok barang (kecuali jika barang sama)
            if ($permintaanBarang->barang_id != $request->barang_id) {
                $barang = Barang::findOrFail($request->barang_id);
            } else {
                $barang = $permintaanBarang->barang;
            }

            // Hitung stok yang diperlukan (selisih dari jumlah sebelumnya)
            $stokDiperlukan = $request->jumlah;
            if ($permintaanBarang->barang_id == $request->barang_id) {
                // Jika barang sama, hitung selisih
                $stokDiperlukan = $request->jumlah - $permintaanBarang->jumlah;
            }

            if ($stokDiperlukan > 0 && $barang->stok < $stokDiperlukan) {
                return redirect()->back()
                    ->withErrors(['jumlah' => 'Stok barang tidak mencukupi. Stok tersedia: ' . $barang->stok])
                    ->withInput();
            }

            // Hitung harga baru
            $hargaSatuan = $barang->harga;
            $totalHarga = $hargaSatuan * $request->jumlah;

            // Update permintaan
            $permintaanBarang->update([
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHarga,
                'tujuan' => $request->tujuan,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->route('permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function approve(Request $request, PermintaanBarang $permintaanBarang)
    {
        // Authorization check - hanya admin dan super admin
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin'])) {
            abort(403);
        }

        // Cek apakah sudah diapprove/direject sebelumnya
        if ($permintaanBarang->status != 'pending') {
            return redirect()->route('permintaan-barang.index')
                ->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $request->validate([
            'catatan_approval' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Update status permintaan
            $permintaanBarang->update([
                'status' => 'approved',
                'disetujui_oleh' => Auth::id(),
                'tanggal_approval' => now(),
                'catatan_approval' => $request->catatan_approval,
            ]);

            // Kurangi stok barang
            $barang = $permintaanBarang->barang;
            $barang->decrement('stok', $permintaanBarang->jumlah);

            DB::commit();

            return redirect()->route('permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil disetujui. Stok barang telah dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function reject(Request $request, PermintaanBarang $permintaanBarang)
    {
        // Authorization check - hanya admin dan super admin
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin'])) {
            abort(403);
        }

        // Cek apakah sudah diapprove/direject sebelumnya
        if ($permintaanBarang->status != 'pending') {
            return redirect()->route('permintaan-barang.index')
                ->with('error', 'Permintaan sudah diproses sebelumnya.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        try {
            $permintaanBarang->update([
                'status' => 'rejected',
                'disetujui_oleh' => Auth::id(),
                'tanggal_approval' => now(),
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            return redirect()->route('permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function cancel(PermintaanBarang $permintaanBarang)
    {
        // Authorization check - hanya pembuat permintaan
        if ($permintaanBarang->user_id != Auth::id()) {
            abort(403);
        }

        // Hanya bisa cancel jika masih pending
        if ($permintaanBarang->status != 'pending') {
            return redirect()->route('permintaan-barang.index')
                ->with('error', 'Tidak dapat membatalkan permintaan yang sudah diproses.');
        }

        try {
            $permintaanBarang->update([
                'status' => 'cancelled',
            ]);

            return redirect()->route('permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(PermintaanBarang $permintaanBarang)
    {
        // Authorization check
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin']) && 
            $permintaanBarang->user_id != Auth::id()) {
            abort(403);
        }

        // Hanya bisa hapus jika pending atau cancelled
        if (!in_array($permintaanBarang->status, ['pending', 'cancelled'])) {
            return redirect()->route('permintaan-barang.index')
                ->with('error', 'Tidak dapat menghapus permintaan yang sudah diproses.');
        }

        try {
            $permintaanBarang->delete();
            return redirect()->route('permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Method untuk melihat history stok
    public function historyStok($barangId)
    {
        $barang = Barang::findOrFail($barangId);
        $permintaan = PermintaanBarang::with(['user', 'approver'])
            ->where('barang_id', $barangId)
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('permintaan-barang.history-stok', compact('barang', 'permintaan'));
    }
}
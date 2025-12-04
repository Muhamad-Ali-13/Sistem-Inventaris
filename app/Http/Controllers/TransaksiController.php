<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaction;
use App\Models\Item;
use App\Models\PermintaanBarang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransaksiController extends Controller
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
        $query = Transaksi::with(['barang', 'user']);

        if (!$this->userHasRole(Auth::user(), 'super admin') && !$this->userHasRole(Auth::user(), 'admin')) {
            $query->where('user_id', Auth::id());
        }

        // Manual search implementation
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%$search%")
                    ->orWhere('notes', 'like', "%$search%")
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->where('nama', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('nama', 'like', "%$search%");
                    });
            });
        }

        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe', $request->tipe);
        }

        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('tanggal_transaksi', $request->tanggal);
        }

        $transaksi = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('transaksi.index', compact('transaksi'));
    }

    public function create()
    {
        $barangs = Barang::all();
        return view('transaksi.create', compact('barangs'));
    }

    public function storeMasuk(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $barang = Barang::findOrFail($request->barang_id);

            // Generate kode transaksi
            $kodeTransaksi = 'TRX-IN-' . date('Ymd') . '-' . strtoupper(uniqid());

            // Hitung total harga
            $totalHarga = $request->jumlah * $request->harga_satuan;

            // Update stok dan harga rata-rata barang
            $barang->updateStokDanHargaRataRata($request->jumlah, $request->harga_satuan, 'masuk');

            // Buat transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal' => $request->tanggal,
                'jenis' => 'masuk',
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'harga_satuan' => $request->harga_satuan,
                'total_harga' => $totalHarga,
                'keterangan' => $request->keterangan,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('transaksi.index')->with('success', 'Transaksi masuk berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function storeKeluar(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $barang = Barang::findOrFail($request->barang_id);

            // Cek stok
            if ($barang->stok < $request->jumlah) {
                throw new \Exception('Stok tidak mencukupi. Stok tersedia: ' . $barang->stok);
            }

            // Generate kode transaksi
            $kodeTransaksi = 'TRX-OUT-' . date('Ymd') . '-' . strtoupper(uniqid());

            // Ambil harga satuan dari harga_rata_rata barang
            $hargaSatuan = $barang->harga_rata_rata;
            $totalHarga = $request->jumlah * $hargaSatuan;

            // Update stok barang (keluar)
            $barang->updateStokDanHargaRataRata($request->jumlah, $hargaSatuan, 'keluar');

            // Buat transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal' => $request->tanggal,
                'jenis' => 'keluar',
                'barang_id' => $request->barang_id,
                'jumlah' => $request->jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHarga,
                'keterangan' => $request->keterangan,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('transaksi.index')->with('success', 'Transaksi keluar berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Transaksi $transaction)
    {
        if (!$this->userHasRole(Auth::user(), 'super admin') && !$this->userHasRole(Auth::user(), 'admin') && $transaction->user_id != Auth::id()) {
            abort(403);
        }

        return view('transaksi.show', compact('transaction'));
    }

    public function approve(Request $request, PermintaanBarang $permintaanBarang)
    {
        // ... cek authorization dan status ...

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

            // Ambil harga satuan dari harga_rata_rata barang saat ini
            $barang = $permintaanBarang->barang;
            $hargaSatuan = $barang->harga_rata_rata;
            $totalHarga = $permintaanBarang->jumlah * $hargaSatuan;

            // Update permintaan dengan harga
            $permintaanBarang->update([
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHarga,
            ]);

            // Update stok barang (keluar)
            $barang->updateStokDanHargaRataRata($permintaanBarang->jumlah, $hargaSatuan, 'keluar');

            // Buat transaksi keluar
            $kodeTransaksi = 'TRX-OUT-' . date('Ymd') . '-' . strtoupper(uniqid());
            Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'tanggal' => now(),
                'jenis' => 'keluar',
                'barang_id' => $permintaanBarang->barang_id,
                'jumlah' => $permintaanBarang->jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHarga,
                'keterangan' => 'Permintaan barang: ' . $permintaanBarang->kode_permintaan,
                'user_id' => Auth::id(),
                'permintaan_barang_id' => $permintaanBarang->id,
            ]);

            DB::commit();

            return redirect()->route('permintaan-barang.index')
                ->with('success', 'Permintaan barang berhasil disetujui. Stok barang telah dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}

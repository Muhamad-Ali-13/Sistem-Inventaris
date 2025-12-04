<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Karyawan;
use App\Models\Kategori;
use App\Models\Barang;
use App\Models\Kendaraan;
use App\Models\PermintaanBarang;
use App\Models\Transaksi;
use App\Models\PenggunaanKendaraan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->hasRole(['super admin', 'admin'])) {
            $data = [
                'departmentCount' => Department::count(),
                'employeeCount' => Karyawan::count(),
                'categoryCount' => Kategori::count(),
                'itemCount' => Barang::count(),
                'vehicleCount' => Kendaraan::count(),
                'requestCount' => PermintaanBarang::count(),
                'transactionOutCount' => Transaksi::where('tipe', 'keluar')->count(),
                'transactionOutThisMonth' => Transaksi::where('tipe', 'keluar')
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->count(),
                'lowStockItems' => Barang::where('stok', '<', DB::raw('stok_minimal'))->get(),
                'pendingRequests' => PermintaanBarang::where('status', 'menunggu')->get(),
                'popularItems' => $this->getPopularItems(),
                'totalPengeluaranBulanIni' => $this->getTotalPengeluaranBulanIni(),
                'pengeluaranPerKaryawan' => $this->getPengeluaranPerKaryawan(),
                'topExpensiveItems' => $this->getTopExpensiveItems(),
                // Data untuk grafik
                'chartPengeluaranBulanan' => $this->getChartPengeluaranBulanan(),
                'chartPermintaanStatus' => $this->getChartPermintaanStatus(),
                'chartKategoriBarang' => $this->getChartKategoriBarang(),
                'chartTrendPengeluaran' => $this->getChartTrendPengeluaran(),
                // Data untuk mini stats
                'rataRataPengeluaran' => $this->getRataRataPengeluaran(),
                'pertumbuhanBulanIni' => $this->getPertumbuhanBulanIni(),
                'approvalRate' => $this->getApprovalRate(),
            ];

            return view('dashboard', $data);
        }

        $karyawanData = [
            'totalPermintaan' => $user->permintaanBarang()->count(),
            'totalPenggunaanKendaraan' => $user->penggunaanKendaraan()->count(),
            'permintaanPending' => $user->permintaanBarang()->where('status', 'menunggu')->count(),
            'totalPengeluaranBulanIni' => $user->getTotalPengeluaranBulanIni(),
            'riwayatPengeluaran' => $user->getRiwayatPengeluaran(Carbon::now()->month, Carbon::now()->year),
            // Data grafik untuk karyawan
            'chartPengeluaranBulanan' => $this->getChartPengeluaranKaryawan($user),
            'chartStatusPermintaan' => $this->getChartStatusPermintaanKaryawan($user),
            // Data stats untuk karyawan
            'totalItemsDisetujui' => $user->permintaanBarang()->where('status', 'disetujui')->sum('jumlah'),
            'successRate' => $this->getSuccessRateKaryawan($user),
            'avgProcessingTime' => $this->getAvgProcessingTimeKaryawan($user),
        ];

        return view('dashboard-karyawan', array_merge($karyawanData, ['user' => $user]));
    }

    private function getPopularItems()
    {
        return Barang::withCount(['transaksi as transaction_count' => function($query) {
                $query->where('tipe', 'keluar')
                    ->where('created_at', '>=', Carbon::now()->subMonth());
            }])
            ->orderBy('transaction_count', 'desc')
            ->take(5)
            ->get();
    }

    private function getTotalPengeluaranBulanIni()
    {
        return PermintaanBarang::where('status', 'disetujui')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_harga');
    }

    private function getPengeluaranPerKaryawan()
    {
        return PermintaanBarang::with('user')
            ->where('status', 'disetujui')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->selectRaw('user_id, SUM(total_harga) as total_pengeluaran')
            ->groupBy('user_id')
            ->orderBy('total_pengeluaran', 'desc')
            ->get();
    }

    private function getTopExpensiveItems()
    {
        return Barang::orderBy('harga', 'desc')
            ->take(5)
            ->get();
    }

    // ==================== METHOD GRAFIK UNTUK ADMIN ====================

    private function getChartPengeluaranBulanan()
    {
        $currentYear = Carbon::now()->year;
        
        $pengeluaran = PermintaanBarang::where('status', 'disetujui')
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as bulan, SUM(total_harga) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->translatedFormat('F');
            $found = $pengeluaran->firstWhere('bulan', $i);
            $data[] = $found ? (float)$found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getChartPermintaanStatus()
    {
        $statusCounts = PermintaanBarang::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $labels = ['Menunggu', 'Disetujui', 'Ditolak'];
        $data = [
            (int)($statusCounts->where('status', 'menunggu')->first()->total ?? 0),
            (int)($statusCounts->where('status', 'disetujui')->first()->total ?? 0),
            (int)($statusCounts->where('status', 'ditolak')->first()->total ?? 0),
        ];

        $colors = ['#ffc107', '#28a745', '#dc3545'];

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors
        ];
    }

    private function getChartKategoriBarang()
    {
        $kategoriBarang = Barang::with('kategori')
            ->selectRaw('kategori_id, COUNT(*) as total, SUM(harga * stok) as total_nilai')
            ->groupBy('kategori_id')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#007bff', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6610f2'];

        foreach ($kategoriBarang as $index => $item) {
            $labels[] = $item->kategori->nama;
            $data[] = (int)$item->total;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels))
        ];
    }

    private function getChartTrendPengeluaran()
    {
        $currentYear = Carbon::now()->year;
        
        $trend = PermintaanBarang::where('status', 'disetujui')
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as bulan, SUM(total_harga) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->translatedFormat('M');
            $found = $trend->firstWhere('bulan', $i);
            $data[] = $found ? (float)$found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    // ==================== METHOD STATS UNTUK ADMIN ====================

    private function getRataRataPengeluaran()
    {
        $data = $this->getChartPengeluaranBulanan()['data'];
        $nonZeroData = array_filter($data, function($value) {
            return $value > 0;
        });
        
        if (count($nonZeroData) > 0) {
            return array_sum($nonZeroData) / count($nonZeroData);
        }
        
        return 0;
    }

    private function getPertumbuhanBulanIni()
    {
        $data = $this->getChartPengeluaranBulanan()['data'];
        $currentMonthIndex = date('n') - 1;
        $prevMonthIndex = $currentMonthIndex - 1;
        
        // Pastikan index tidak negatif
        if ($prevMonthIndex < 0) {
            return 0;
        }
        
        $currentMonth = $data[$currentMonthIndex] ?? 0;
        $prevMonth = $data[$prevMonthIndex] ?? 0;
        
        // Hindari division by zero
        if ($prevMonth > 0) {
            return (($currentMonth - $prevMonth) / $prevMonth) * 100;
        }
        
        // Jika bulan sebelumnya nol dan bulan ini ada nilai, anggap growth 100%
        if ($currentMonth > 0) {
            return 100;
        }
        
        return 0;
    }

    private function getApprovalRate()
    {
        $statusData = $this->getChartPermintaanStatus()['data'];
        $total = array_sum($statusData);
        $approved = $statusData[1] ?? 0; // Index 1 adalah 'Disetujui'
        
        if ($total > 0) {
            return ($approved / $total) * 100;
        }
        
        return 0;
    }

    // ==================== METHOD GRAFIK UNTUK KARYAWAN ====================

    private function getChartPengeluaranKaryawan($user)
    {
        $currentYear = Carbon::now()->year;
        
        $pengeluaran = $user->permintaanBarang()
            ->where('status', 'disetujui')
            ->whereYear('created_at', $currentYear)
            ->selectRaw('MONTH(created_at) as bulan, SUM(total_harga) as total')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->translatedFormat('F');
            $found = $pengeluaran->firstWhere('bulan', $i);
            $data[] = $found ? (float)$found->total : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getChartStatusPermintaanKaryawan($user)
    {
        $statusCounts = $user->permintaanBarang()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $labels = ['Menunggu', 'Disetujui', 'Ditolak'];
        $data = [
            (int)($statusCounts->where('status', 'menunggu')->first()->total ?? 0),
            (int)($statusCounts->where('status', 'disetujui')->first()->total ?? 0),
            (int)($statusCounts->where('status', 'ditolak')->first()->total ?? 0),
        ];

        $colors = ['#ffc107', '#28a745', '#dc3545'];

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors
        ];
    }

    // ==================== METHOD STATS UNTUK KARYAWAN ====================

    private function getSuccessRateKaryawan($user)
    {
        $total = $user->permintaanBarang()->count();
        $approved = $user->permintaanBarang()->where('status', 'disetujui')->count();
        
        if ($total > 0) {
            return ($approved / $total) * 100;
        }
        
        return 0;
    }

    private function getAvgProcessingTimeKaryawan($user)
    {
        $approvedRequests = $user->permintaanBarang()
            ->where('status', 'disetujui')
            ->whereNotNull('disetujui_oleh')
            ->whereNotNull('updated_at')
            ->get();

        $totalDays = 0;
        $count = 0;

        foreach ($approvedRequests as $request) {
            if ($request->created_at && $request->updated_at) {
                $totalDays += $request->created_at->diffInDays($request->updated_at);
                $count++;
            }
        }

        if ($count > 0) {
            return $totalDays / $count;
        }

        return 0;
    }
}
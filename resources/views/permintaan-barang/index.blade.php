@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Permintaan Barang</h3>
                        <div class="card-tools">
                            @auth
                                @if (auth()->user()->hasRole(['super admin', 'admin']) || auth()->user()->hasRole('karyawan'))
                                    <a href="{{ route('permintaan-barang.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Permintaan Barang
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Filter Form -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <form method="GET" action="{{ route('permintaan-barang.index') }}">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Cari barang, tujuan, atau peminta..."
                                            value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">Cari</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('permintaan-barang.index') }}">
                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                            Disetujui</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                            Ditolak</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                            Dibatalkan</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Kode Permintaan</th>
                                        <th>Barang</th>
                                        <th>Peminta</th>
                                        <th>Tujuan</th>
                                        <th>Jumlah</th>
                                        <th>Harga Satuan</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                        <th>Tanggal Permintaan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($requests as $request)
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ $request->kode_permintaan }}</td>
                                            <td>
                                                {{ $request->barang->nama }}
                                                <br>
                                                <small class="text-muted">Stok: {{ $request->barang->stok }}</small>
                                            </td>
                                            <td>{{ $request->user->name }}</td>
                                            <td>{{ Str::limit($request->tujuan, 50) }}</td>
                                            <td>{{ $request->jumlah }}</td>
                                            <td>Rp {{ number_format($request->harga_satuan, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($request->total_harga, 0, ',', '.') }}</td>
                                            <td>
                                                @if ($request->status == 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($request->status == 'approved')
                                                    <span class="badge badge-success">Disetujui</span>
                                                @elseif($request->status == 'rejected')
                                                    <span class="badge badge-danger">Ditolak</span>
                                                @elseif($request->status == 'cancelled')
                                                    <span class="badge badge-secondary">Dibatalkan</span>
                                                @endif
                                            </td>
                                            <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('permintaan-barang.show', $request) }}"
                                                    class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>

                                                @if ($request->status == 'pending' && $request->user_id == auth()->id())
                                                    <a href="{{ route('permintaan-barang.edit', $request) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif

                                                @if (auth()->user()->hasRole(['super admin', 'admin']))
                                                    <!-- Approval Actions -->
                                                    @if ($request->status == 'pending')
                                                        <form action="{{ route('permintaan-barang.approve', $request) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Setujui permintaan ini?')">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check"></i> Setujui
                                                            </button>
                                                        </form>

                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#rejectModal{{ $request->id }}">
                                                            <i class="fas fa-times"></i> Tolak
                                                        </button>

                                                        <!-- Reject Modal -->
                                                        <div class="modal fade" id="rejectModal{{ $request->id }}"
                                                            tabindex="-1">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Tolak Permintaan Barang</h5>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal">
                                                                            <span>&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <form
                                                                        action="{{ route('permintaan-barang.reject', $request) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <label for="alasan_penolakan">Alasan
                                                                                    Penolakan</label>
                                                                                <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="3" required
                                                                                    placeholder="Masukkan alasan penolakan..."></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary"
                                                                                data-dismiss="modal">Batal</button>
                                                                            <button type="submit"
                                                                                class="btn btn-danger">Tolak
                                                                                Permintaan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    <!-- User Actions -->
                                                    @if ($request->status == 'pending' && $request->user_id == auth()->id())
                                                        <form action="{{ route('permintaan-barang.destroy', $request) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Batalkan permintaan ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-times"></i> Batalkan
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($requests->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $requests->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .badge {
            font-size: 0.8em;
        }

        .btn-group-sm>.btn,
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@endpush

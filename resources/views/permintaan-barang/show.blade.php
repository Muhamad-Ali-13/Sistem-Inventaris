@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Permintaan Barang</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Kode Permintaan</th>
                                    <td>{{ $permintaanBarang->kode_permintaan }}</td>
                                </tr>
                                <tr>
                                    <th>Barang</th>
                                    <td>
                                        {{ $permintaanBarang->barang->nama }}
                                        <br>
                                        <small class="text-muted">Kode: {{ $permintaanBarang->barang->kode }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Diminta Oleh</th>
                                    <td>{{ $permintaanBarang->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Jumlah</th>
                                    <td>{{ $permintaanBarang->jumlah }}</td>
                                </tr>
                                <tr>
                                    <th>Stok Saat Ini</th>
                                    <td>
                                        @if($permintaanBarang->barang->stok < $permintaanBarang->barang->stok_minimal)
                                            <span class="badge badge-danger">{{ $permintaanBarang->barang->stok }} (Stok Rendah)</span>
                                        @else
                                            <span class="badge badge-success">{{ $permintaanBarang->barang->stok }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>
                                        @if($permintaanBarang->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($permintaanBarang->status == 'approved')
                                            <span class="badge badge-success">Disetujui</span>
                                        @elseif($permintaanBarang->status == 'rejected')
                                            <span class="badge badge-danger">Ditolak</span>
                                        @elseif($permintaanBarang->status == 'cancelled')
                                            <span class="badge badge-secondary">Dibatalkan</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Permintaan</th>
                                    <td>{{ $permintaanBarang->created_at->format('d F Y H:i') }}</td>
                                </tr>
                                @if($permintaanBarang->disetujui_oleh)
                                <tr>
                                    <th>Disetujui Oleh</th>
                                    <td>{{ $permintaanBarang->approver->name ?? 'User tidak ditemukan' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Persetujuan</th>
                                    <td>{{ $permintaanBarang->tanggal_approval ? $permintaanBarang->tanggal_approval->format('d F Y H:i') : '-' }}</td>
                                </tr>
                                @endif
                                @if($permintaanBarang->alasan_penolakan)
                                <tr>
                                    <th>Alasan Penolakan</th>
                                    <td>{{ $permintaanBarang->alasan_penolakan }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Tujuan</h5>
                            <div class="border p-3 bg-light rounded">
                                {{ $permintaanBarang->tujuan }}
                            </div>
                        </div>
                    </div>

                    @if($permintaanBarang->keterangan)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Keterangan</h5>
                            <div class="border p-3 bg-light rounded">
                                {{ $permintaanBarang->keterangan }}
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <a href="{{ route('permintaan-barang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                            </a>
                            
                            @if(auth()->user()->hasRole(['super admin', 'admin']))
                                @if($permintaanBarang->status == 'pending')
                                    <form action="{{ route('permintaan-barang.approve', $permintaanBarang) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui permintaan ini?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                @endif
                            @else
                                @if($permintaanBarang->status == 'pending' && $permintaanBarang->user_id == auth()->id())
                                    <a href="{{ route('permintaan-barang.edit', $permintaanBarang) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('permintaan-barang.destroy', $permintaanBarang) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan permintaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-times"></i> Batalkan Permintaan
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Barang</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h4">{{ $permintaanBarang->barang->nama }}</div>
                        <div class="text-muted">{{ $permintaanBarang->barang->kategori->nama }}</div>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Kode:</strong></td>
                            <td>{{ $permintaanBarang->barang->kode }}</td>
                        </tr>
                        <tr>
                            <td><strong>Stok Saat Ini:</strong></td>
                            <td>
                                <span class="badge badge-{{ $permintaanBarang->barang->stok > 0 ? 'success' : 'danger' }}">
                                    {{ $permintaanBarang->barang->stok }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Stok Minimal:</strong></td>
                            <td>{{ $permintaanBarang->barang->stok_minimal }}</td>
                        </tr>
                        <tr>
                            <td><strong>Lokasi:</strong></td>
                            <td>{{ $permintaanBarang->barang->lokasi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Harga:</strong></td>
                            <td>Rp {{ number_format($permintaanBarang->barang->harga, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    @if($permintaanBarang->barang->deskripsi)
                    <div class="mt-3">
                        <strong>Deskripsi:</strong>
                        <p class="text-muted small">{{ $permintaanBarang->barang->deskripsi }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if(auth()->user()->hasRole(['super admin', 'admin']) && $permintaanBarang->status == 'pending')
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Permintaan Barang</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('permintaan-barang.reject', $permintaanBarang) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="alasan_penolakan">Alasan Penolakan *</label>
                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" 
                                  rows="4" required placeholder="Berikan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Permintaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
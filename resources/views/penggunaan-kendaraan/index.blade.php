@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Data Penggunaan Kendaraan</h3>
                    @if(auth()->user()->hasRole(['super admin', 'admin']) || auth()->user()->hasRole('karyawan'))
                    <a href="{{ route('penggunaan-kendaraan.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Ajukan Peminjaman
                    </a>
                    @endif
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="penggunaan-kendaraan-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Karyawan</th>
                                    <th>Kendaraan</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Tujuan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usages as $usage)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $usage->user->name }}</td>
                                    <td>{{ $usage->kendaraan->nama }} ({{ $usage->kendaraan->plat_nomor }})</td>
                                    <td>{{ $usage->tanggal_mulai->format('d/m/Y') }}</td>
                                    <td>{{ $usage->tanggal_selesai->format('d/m/Y') }}</td>
                                    <td>{{ Str::limit($usage->tujuan, 50) }}</td>
                                    <td>
                                        @if($usage->status == 'menunggu')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @elseif($usage->status == 'disetujui')
                                            <span class="badge badge-success">Disetujui</span>
                                        @elseif($usage->status == 'ditolak')
                                            <span class="badge badge-danger">Ditolak</span>
                                        @else
                                            <span class="badge badge-info">Dikembalikan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('penggunaan-kendaraan.show', $usage->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->hasRole(['super admin', 'admin']))
                                            @if($usage->status == 'menunggu')
                                            <div class="btn-group">
                                                <form action="{{ route('penggunaan-kendaraan.approve', $usage->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Setujui peminjaman kendaraan ini?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $usage->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            @elseif($usage->status == 'disetujui')
                                            <form action="{{ route('penggunaan-kendaraan.return', $usage->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Tandai kendaraan sudah dikembalikan?')">
                                                    <i class="fas fa-undo"></i> Kembalikan
                                                </button>
                                            </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                                <!-- Modal Penolakan -->
                                @if(auth()->user()->hasRole(['super admin', 'admin']))
                                <div class="modal fade" id="rejectModal{{ $usage->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('penggunaan-kendaraan.reject', $usage->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Tolak Peminjaman Kendaraan</h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Alasan Penolakan</label>
                                                        <textarea name="alasan_penolakan" class="form-control" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak Peminjaman</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#penggunaan-kendaraan-table').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            }
        });
    });
</script>
@endpush
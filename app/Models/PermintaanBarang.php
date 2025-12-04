<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    use HasFactory;

    protected $table = 'permintaan_barang';

    protected $fillable = [
        'kode_permintaan',
        'user_id',
        'barang_id',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'tujuan',
        'keterangan',
        'status',
        'disetujui_oleh',
        'alasan_penolakan',
        'catatan_approval',
        'tanggal_approval',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
        'tanggal_approval' => 'datetime',
    ];

    // Relationship ke user yang membuat permintaan
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship ke barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    // Relationship ke user yang menyetujui/menolak (approver)
    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Scope untuk status pending
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope untuk status approved
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope untuk status rejected
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Scope untuk status cancelled
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Cek apakah bisa diedit (hanya pending)
    public function canEdit()
    {
        return $this->status === 'pending';
    }

    // Cek apakah bisa disetujui (hanya pending)
    public function canApprove()
    {
        return $this->status === 'pending';
    }

    // Cek apakah bisa ditolak (hanya pending)
    public function canReject()
    {
        return $this->status === 'pending';
    }

    // Cek apakah bisa dibatalkan (hanya pending)
    public function canCancel()
    {
        return $this->status === 'pending';
    }

    // Cek apakah bisa dihapus (pending atau cancelled)
    public function canDelete()
    {
        return in_array($this->status, ['pending', 'cancelled']);
    }

    // Di model PermintaanBarang.php
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'approved' => '<span class="badge bg-success">Disetujui</span>',
            'rejected' => '<span class="badge bg-danger">Ditolak</span>',
            'cancelled' => '<span class="badge bg-secondary">Dibatalkan</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
        ];

        return $texts[$this->status] ?? 'Unknown';
    }
}

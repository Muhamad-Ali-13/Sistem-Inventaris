<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';
    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'department_id',
        'jabatan',
    ];

    protected $casts = [
        // Add casts if needed
    ];

    // Relasi ke user - menggunakan hasOne dengan foreign key di tabel users
    public function user()
    {
        // Parameter 1: Model User
        // Parameter 2: Foreign key di tabel users (karyawan_id)
        // Parameter 3: Local key di tabel karyawan (id)
        return $this->hasOne(User::class, 'karyawan_id', 'id');
    }

    // Scope untuk karyawan yang belum punya user
    public function scopeBelumPunyaUser($query)
    {
        return $query->whereDoesntHave('user');
    }

    // Relasi ke department (jika ada)
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
}

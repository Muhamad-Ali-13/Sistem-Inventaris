<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'department_id', 'position'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class);
    }

    public function vehicleUsages()
    {
        return $this->hasMany(VehicleUsage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

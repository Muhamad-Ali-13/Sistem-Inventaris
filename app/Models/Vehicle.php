<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
     use HasFactory;

    protected $fillable = ['name', 'license_plate', 'type', 'is_available'];

    public function vehicleUsages()
    {
        return $this->hasMany(VehicleUsage::class);
    }
}

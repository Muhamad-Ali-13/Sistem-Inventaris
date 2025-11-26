<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'category_id', 'stock', 'min_stock', 'location'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class, 'item_id');
    }

    // Scope for low stock items
    public function scopeLowStock($query)
    {
        return $query->where('stock', '<', DB::raw('min_stock'));
    }
}

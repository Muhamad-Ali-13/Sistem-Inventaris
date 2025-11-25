<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
     use HasFactory;

    protected $fillable = ['name', 'description', 'category_id', 'stock', 'min_stock', 'location'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

/*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Get all of the employees for the department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
/*******  2e012fd7-e32d-41a2-acaa-82809cffbd74  *******/
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}

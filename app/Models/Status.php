<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    // Custom primary key
    protected $primaryKey = 'status_id';
    public $incrementing = false; // Disable auto-increment for custom IDs

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'status_id',
        'status_name',
    ];

    // Relationships
    /**
     * Get the employees with this status.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'status_id', 'status_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    // Custom primary key
    protected $primaryKey = 'office_id';
    public $incrementing = false; // Disable auto-increment

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'office_id',
        'office_name',
    ];

    // Relationships
    /**
     * Get the campus that owns the office.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id', 'campus_id'); 
        // Assuming `campus_id` exists in the `offices` table
    }

    /**
     * Get the employees for the office.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'office_id', 'office_id');
    }
}

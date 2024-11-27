<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // Custom primary key
    protected $primaryKey = 'emp_id';
    public $incrementing = false; // Disable auto-increment for custom IDs

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'emp_id',
        'emp_fname',
        'emp_lname',
        'emp_mname',
        'emp_contact',
        'emp_email',
        'campus_id',
        'office_id',
        'status_id',
        'type_id',
    ];

    // Relationships
    /**
     * Get the campus associated with the employee.
     */
    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id', 'campus_id');
    }

    /**
     * Get the office associated with the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'office_id');
    }

    /**
     * Get the status associated with the employee.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }

    /**
     * Get the type associated with the employee.
     */
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id', 'type_id');
    }
}

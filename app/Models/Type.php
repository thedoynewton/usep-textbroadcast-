<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    // Custom primary key
    protected $primaryKey = 'type_id';
    public $incrementing = false; // Disable auto-increment for custom IDs

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'type_id',
        'type_name',
    ];

    // Relationships
    /**
     * Get the employees associated with this type.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'type_id', 'type_id');
    }
}

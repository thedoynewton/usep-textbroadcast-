<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;
    protected $primaryKey = 'type_id';

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'type_id');
    }
}

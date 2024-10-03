<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;
    protected $primaryKey = 'campus_id';

    public function colleges()
    {
        return $this->hasMany(College::class, 'campus_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'campus_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'campus_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'campus_id');
    }

    public function majors()
    {
        return $this->hasMany(Major::class, 'campus_id');
    }

    public function offices()
    {
        return $this->hasMany(Office::class, 'campus_id');
    }
}

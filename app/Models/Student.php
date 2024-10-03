<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $primaryKey = 'stud_id';

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function college()
    {
        return $this->belongsTo(College::class, 'college_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
}

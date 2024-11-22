<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // The table does not use auto-incrementing keys
    public $incrementing = false;

    // The primary key is composite
    protected $primaryKey = ['stud_id', 'campus_id'];

    // Specify the key type for composite primary key
    protected $keyType = 'string'; // `stud_id` is a string

    protected $fillable = [
        'stud_id',
        'stud_fname',
        'stud_lname',
        'stud_mname',
        'stud_contact',
        'stud_email',
        'campus_id',
        'college_id',
        'program_id',
        'major_id',
        'year_id',
        'enrollment_stat',
        'created_at',
        'updated_at',
    ];

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    use HasFactory;
    protected $primaryKey = 'college_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'college_id',
        'campus_id',
        'college_name',
        'created_at',
        'updated_at',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'college_id');
    }

    public function majors()
    {
        return $this->hasMany(Major::class, 'college_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'college_id');
    }
}

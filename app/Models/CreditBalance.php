<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditBalance extends Model
{
    use HasFactory;
    protected $table = 'credit_balances';

    // Specify the primary key if it's not `id`
    protected $primaryKey = 'id';

    // Define fillable properties
    protected $fillable = [
        'balance',
    ];

    // Disable timestamps if you don’t need created_at and updated_at
    public $timestamps = true;
}

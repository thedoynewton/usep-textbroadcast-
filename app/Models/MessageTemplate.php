<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name', 'content'];

    public function category()
    {
        return $this->belongsTo(MessageCategory::class);
    }
}

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
        return $this->belongsTo(MessageCategory::class, 'category_id');
    }

    // Link to MessageLogs
    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class, 'template_name', 'name');
    }
}

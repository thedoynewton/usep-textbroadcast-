<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

        /**
     * Get the message templates associated with this category.
     */
    public function messageTemplates()
    {
        return $this->hasMany(MessageTemplate::class, 'category_id');
    }    

        /**
     * Get the message logs associated with this category.
     */
    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class, 'category_id');
    }
}

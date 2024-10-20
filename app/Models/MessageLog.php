<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'campus_id',
        'recipient_type',
        'content',
        'message_type',
        'scheduled_at',
        'sent_at',
        'cancelled_at',
        'status',
        'total_recipients',
        'sent_count',
        'failed_count'
    ];

    // Add casting for sent_at and scheduled_at fields
    protected $casts = [
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function recipients()
    {
        return $this->hasMany(MessageRecipient::class);
    }
}

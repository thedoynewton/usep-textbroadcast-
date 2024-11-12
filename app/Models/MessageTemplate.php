<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name', 'content'];

    public function getTemplatesByCategory($categoryId)
{
    $templates = MessageTemplate::where('category_id', $categoryId)->get(['id', 'name', 'content']);
    return response()->json($templates);
}

}

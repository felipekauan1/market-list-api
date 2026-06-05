<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'list_id',
        'name',
        'category',
        'quantity',
        'purchased',
        'notes',
    ];

    protected $casts = [
        'purchased' => 'boolean',
    ];

    public function list()
    {
        return $this->belongsTo(ShoppingList::class, 'list_id');
    }
}

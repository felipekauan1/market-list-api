<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $table = 'lists';

    protected $fillable = [
        'user_id',
        'month',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'list_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'barcode',
        'price',
        'quantity',
        'status',
        'user_id',
        'white_label_id',
    ];
    public function scopeForUser($query, $user)
    {
        if ($user->white_label_id === null) {
            return $query;
        }
        return $query->where('white_label_id', $user->white_label_id);
    }
}

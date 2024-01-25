<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable =[
        'price',
        'quantity',
        'product_id',
        'order_id',
        'days',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function scopeForUser($query, $user)
    {
        if ($user->white_label_id === null) {
            return $query;
        }
        
        return $query->whereHas('product', function ($query) use ($user) {
            $query->where('white_label_id', $user->white_label_id);
        });
    }

}

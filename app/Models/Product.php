<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


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
    public function scopeMostSelling($query, $user)
    {
        return $query->forUser($user)->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_quantity'))
            ->where('order_items.created_at', '>=', now()->subDays(30))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5);
    }
    
    
    
}

<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderImage extends Model
{
    protected $fillable = [
        'image',
        'description',
        'order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

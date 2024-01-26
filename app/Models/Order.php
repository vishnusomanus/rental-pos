<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'white_label_id',
        'proof',
        'notes'
    ];
    public function scopeForUser($query, $user)
    {
        if ($user->white_label_id === null) {
            return $query;
        }
        return $query->where('white_label_id', $user->white_label_id);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function orderImages()
    {
        return $this->hasMany(OrderImage::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCustomerName()
    {
        if($this->customer) {
            return $this->customer->first_name . ' ' . $this->customer->last_name;
        }
        return __('customer.working');
    }

    public function total()
    {
        return $this->items->map(function ($i){
            return $i->price;
        })->sum();
    }

    public function formattedTotal()
    {
        return number_format($this->total(), 2);
    }
    public function balance()
    {
        return $this->total() - $this->receivedAmount();
    }

    public function receivedAmount()
    {
        return $this->payments->map(function ($i){
            return $i->amount;
        })->sum();
    }

    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }
}

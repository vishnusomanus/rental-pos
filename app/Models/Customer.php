<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'avatar',
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

    public function getAvatarUrl()
    {
        return Storage::url($this->avatar);
    }
}

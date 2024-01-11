<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhiteLabel extends Model
{
    use HasFactory;
    protected $table = 'white_labels';
    protected $fillable = ['domain', 'description', 'url'];
}

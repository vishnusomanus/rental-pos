<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\WhiteLabel;

class Setting extends Model
{
    
    protected $fillable = [
        'key', 'value', 'white_label_id'
    ];
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('whiteLabel', function (Builder $builder) {
            $whiteLabels = WhiteLabel::where('domain', request()->getHost())->first();
            $builder->where('white_label_id', $whiteLabels->id);
        });
    }
}

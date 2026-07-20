<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    protected $table = 'loyalty_settings';

    protected $fillable = [
        'key',
        'key_ar',
        'key_en',
        'value_ar',
        'value_en',
    ];

    public function getKeyLabelAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->key_ar : $this->key_en;
    }

    public function getValueAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->value_ar : $this->value_en;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoutineStep extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'order',
    ];
    public function getNameAttribute($value)
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }
    public function products()
    {
        return $this->hasMany(ProductRoutine::class);
    } 
}

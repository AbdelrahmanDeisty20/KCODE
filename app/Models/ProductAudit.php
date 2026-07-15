<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAudit extends Model
{
    protected $fillable = [
        'product_id',
        'avoid_pairing_same_routine',
        'developer_output_rule',
        'show_alternatives_button',
        'remove_if_customer_has_it',
        'source_url',
        'data_confidence',
        'needs_manual_check',
    ];

    protected $casts = [
        'show_alternatives_button' => 'boolean',
        'remove_if_customer_has_it' => 'boolean',
        'needs_manual_check' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

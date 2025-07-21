<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sk_order_details';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sk_order_details',
        'order_id',
        'item_name',
        'description',
        'item_price',
        'number_of_item',
        'active',
        'created_by',
        'last_modified_by'
    ];

    protected $casts = [
        'item_price' => 'decimal:2',
        'number_of_item' => 'integer',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'sk_order');
    }
}
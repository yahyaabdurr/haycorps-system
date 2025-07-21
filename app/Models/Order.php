<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sk_order';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sk_order',
        'order_id',
        'file_url',
        'pic_employee',
        'order_date',
        'completion_date',
        'order_status',
        'total_price',
        'active',
        'customer_id',
        'created_by',
        'last_modified_by'
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'completion_date' => 'datetime',
        'total_price' => 'decimal:2',
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'sk_customer');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'sk_order');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'sk_order');
    }
}
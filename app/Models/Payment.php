<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sk_transaction';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sk_transaction',
        'order_id',
        'transaction_id',
        'type',
        'amount',
        'method',
        'description',
        'active',
        'created_by',
        'last_modified_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
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
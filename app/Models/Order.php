<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ensure sk_order is set
            if (empty($model->sk_order)) {
                $model->sk_order = Str::orderedUuid()->toString();
            }
            // Ensure order_id is set if empty
            if (empty($model->order_id)) {
                $model->order_id = 'ORDER-' . Str::upper(Str::random(6));
            }

            if (empty($model->order_date)) {
                $model->order_date = now();
            }
            // Set audit fields
            $model->created_by = $model->created_by ?? (auth()->user()?->name ?? 'SYSTEM');
            $model->last_modified_by = $model->last_modified_by ?? (auth()->user()?->name ?? 'SYSTEM');
        });

        static::updating(function ($model) {
            $model->last_modified_by = auth()->user()?->name ?? 'SYSTEM';
        });
    }


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

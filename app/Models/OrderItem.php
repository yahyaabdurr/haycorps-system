<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ensure sk_order_details is set
            if (empty($model->sk_order_details)) {
                $model->sk_order_details = Str::orderedUuid()->toString();
            }

            if (empty($model->item_name)) {
                $model->item_name = $model->product?->product_name ?? 'Unknown Item';
            }
            // Set audit fields
            $model->created_by = $model->created_by ?? (auth()->user()?->name ?? 'SYSTEM');
            $model->last_modified_by = $model->last_modified_by ?? (auth()->user()?->name ?? 'SYSTEM');
        });

        static::updating(function ($model) {
            $model->last_modified_by = auth()->user()?->name ?? 'SYSTEM';
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'sk_order');
    }
}

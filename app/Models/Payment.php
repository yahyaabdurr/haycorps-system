<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ensure sk_transaction is set
            if (empty($model->sk_transaction)) {
                $model->sk_transaction = Str::orderedUuid()->toString();
            }

            if (empty($model->transaction_id)) {
                $model->transaction_id = 'TRX-' . Str::upper(Str::random(6));
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

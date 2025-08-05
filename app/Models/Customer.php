<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sk_customer';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sk_customer',
        'customer_id',
        'customer_name',
        'email',
        'phone_number',
        'address1',
        'address2',
        'institution',
        'active',
        'created_by',
        'last_modified_by'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ensure sk_customer is set
            if (empty($model->sk_customer)) {
                $model->sk_customer = Str::orderedUuid()->toString();
            }
            // Ensure customer_id is set if empty
            if (empty($model->customer_id)) {
                $model->customer_id = 'CUST-' . Str::upper(Str::random(6));
            }
            // Set audit fields
            $model->created_by = $model->created_by ?? (auth()->user()?->name ?? 'SYSTEM');
            $model->last_modified_by = $model->last_modified_by ?? (auth()->user()?->name ?? 'SYSTEM');
        });

        static::updating(function ($model) {
            $model->last_modified_by = auth()->user()?->name ?? 'SYSTEM';
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'sk_customer');
    }
}

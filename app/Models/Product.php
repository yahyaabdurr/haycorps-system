<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sk_product';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sk_product',
        'product_code',
        'product_name',
        'description',
        'price',
        'stock',
        'category',
        'image_url',
        'active',
        'created_by',
        'last_modified_by',
        'weight',
        'volume',
        'cost'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'active' => 'boolean',
        'stock' => 'integer',
        'weight' => 'decimal:2',
        'volume' => 'decimal:2',
        'cost' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->sk_product)) {
                $model->sk_product = Str::orderedUuid()->toString();
            }
            if (empty($model->product_code)) {
                $model->product_code = 'PROD-' . Str::upper(Str::random(6));
            }
            $model->created_by = $model->created_by ?? (auth()->user()?->name ?? 'SYSTEM');
            $model->last_modified_by = $model->last_modified_by ?? (auth()->user()?->name ?? 'SYSTEM');
        });

        static::updating(function ($model) {
            $model->last_modified_by = auth()->user()?->name ?? 'SYSTEM';
        });
    }
}

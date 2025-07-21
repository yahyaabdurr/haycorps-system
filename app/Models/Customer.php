<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'sk_customer');
    }
}
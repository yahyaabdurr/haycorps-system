<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
     public function boot(): void
    {
        // Configure Str to use ordered UUIDs
        Str::createUuidsUsing(function () {
            return (string) Str::orderedUuid();
        });

        // Customer Model Observers
        Customer::creating(function ($model) {
            $model->sk_customer = Str::orderedUuid();
            $model->created_by = auth()->id() ?? 'system';
            $model->last_modified_by = auth()->id() ?? 'system';
        });

        Customer::updating(function ($model) {
            $model->last_modified_by = auth()->id() ?? 'system';
        });

        // Order Model Observers
        Order::creating(function ($model) {
            $model->sk_order = Str::orderedUuid();
            $model->created_by = auth()->id() ?? 'system';
            $model->last_modified_by = auth()->id() ?? 'system';
        });

        Order::updating(function ($model) {
            $model->last_modified_by = auth()->id() ?? 'system';
        });

        // OrderItem Model Observers
        OrderItem::creating(function ($model) {
            $model->sk_order_details = Str::orderedUuid();
            $model->created_by = auth()->id() ?? 'system';
            $model->last_modified_by = auth()->id() ?? 'system';
        });

        OrderItem::updating(function ($model) {
            $model->last_modified_by = auth()->id() ?? 'system';
        });

        // Payment Model Observers
        Payment::creating(function ($model) {
            $model->sk_transaction = Str::orderedUuid();
            $model->created_by = auth()->id() ?? 'system';
            $model->last_modified_by = auth()->id() ?? 'system';
        });

        Payment::updating(function ($model) {
            $model->last_modified_by = auth()->id() ?? 'system';
        });

    
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\PaymentResource;



class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'skOrder' => $this->sk_order,
            'orderId' => $this->order_id,
            'fileUrl' => $this->file_url,
            'picEmployee' => $this->pic_employee,
            'orderDate' => $this->order_date->toIso8601String(),
            'completionDate' => $this->completion_date?->toIso8601String(),
            'orderStatus' => $this->order_status,
            'totalPrice' => (float) $this->total_price,
            'active' => $this->active,
            'createdBy' => $this->created_by,
            'createdDate' => $this->created_at->toIso8601String(),
            'lastModifiedBy' => $this->last_modified_by,
            'lastModifiedDate' => $this->updated_at->toIso8601String(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'orderItems' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'paymentHistory' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}

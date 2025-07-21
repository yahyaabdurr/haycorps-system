<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'skTransaction' => $this->sk_transaction,
            'orderId' => $this->order_id,
            'transactionId' => $this->transaction_id,
            'type' => $this->type,
            'amount' => (float) $this->amount,
            'method' => $this->method,
            'description' => $this->description,
            'active' => $this->active,
            'createdBy' => $this->created_by,
            'createdDate' => $this->created_at->toIso8601String(),
            'lastModifiedBy' => $this->last_modified_by,
            'lastModifiedDate' => $this->updated_at->toIso8601String(),
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
}

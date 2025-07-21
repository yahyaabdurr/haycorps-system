<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
    {
        return [
            'skOrderDetails' => $this->sk_order_details,
            'orderId' => $this->order_id,
            'itemName' => $this->item_name,
            'description' => $this->description,
            'itemPrice' => (float) $this->item_price,
            'numberOfItem' => (int) $this->number_of_item,
            'active' => $this->active,
            'createdBy' => $this->created_by,
            'createdDate' => $this->created_at->toIso8601String(),
            'lastModifiedBy' => $this->last_modified_by,
            'lastModifiedDate' => $this->updated_at->toIso8601String(),
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
}

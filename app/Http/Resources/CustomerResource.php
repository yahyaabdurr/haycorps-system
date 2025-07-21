<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\OrderResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
    {
        return [
            'skCustomer' => $this->sk_customer,
            'customerId' => $this->customer_id,
            'customerName' => $this->customer_name,
            'email' => $this->email,
            'phoneNumber' => $this->phone_number,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'institution' => $this->institution,
            'active' => $this->active,
            'createdBy' => $this->created_by,
            'createdDate' => $this->created_at->toIso8601String(),
            'lastModifiedBy' => $this->last_modified_by,
            'lastModifiedDate' => $this->updated_at->toIso8601String(),
            'orders' => OrderResource::collection($this->whenLoaded('orders')),
        ];
    }
}

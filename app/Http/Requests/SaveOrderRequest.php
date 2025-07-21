<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skOrder' => 'required|uuid',
            'orderId' => 'required|string|max:50',
            'fileUrl' => 'nullable|string',
            'picEmployee' => 'nullable|string|max:100',
            'orderDate' => 'required|date',
            'completionDate' => 'nullable|date',
            'orderStatus' => 'required|string|max:50',
            'totalPrice' => 'required|numeric',
            'active' => 'required|boolean',
            'lastModifiedBy' => 'required|string|max:100',
            'lastModifiedDate' => 'required|date',
            'createdDate' => 'required|date',
            'createdBy' => 'required|string|max:100',
            
            'customer' => 'required|array',
            'customer.skCustomer' => 'required|uuid',
            'customer.customerId' => 'required|string|max:50',
            'customer.customerName' => 'required|string|max:100',
            'customer.email' => 'nullable|email|max:100',
            'customer.phoneNumber' => 'nullable|string|max:20',
            'customer.address1' => 'nullable|string',
            'customer.address2' => 'nullable|string',
            'customer.active' => 'required|boolean',
            'customer.createdBy' => 'required|string|max:100',
            'customer.createdDate' => 'required|date',
            'customer.lastModifiedBy' => 'required|string|max:100',
            'customer.lastModifiedDate' => 'required|date',
            'customer.institution' => 'nullable|string|max:100',
            
            'orderItems' => 'required|array',
            'orderItems.*.skOrderDetails' => 'required|uuid',
            'orderItems.*.orderId' => 'required|string|max:50',
            'orderItems.*.itemName' => 'required|string|max:100',
            'orderItems.*.description' => 'nullable|string',
            'orderItems.*.itemPrice' => 'required|numeric',
            'orderItems.*.numberOfItem' => 'required|integer|min:1',
            'orderItems.*.active' => 'required|boolean',
            'orderItems.*.createdDate' => 'required|date',
            'orderItems.*.createdBy' => 'required|string|max:100',
            'orderItems.*.lastModifiedBy' => 'required|string|max:100',
            'orderItems.*.lastModifiedDate' => 'required|date',
            
            'paymentHistory' => 'sometimes|array',
            'paymentHistory.*.skTransaction' => 'required|uuid',
            'paymentHistory.*.orderId' => 'required|string|max:50',
            'paymentHistory.*.transactionId' => 'required|string|max:50',
            'paymentHistory.*.type' => 'required|string|max:50',
            'paymentHistory.*.amount' => 'required|numeric',
            'paymentHistory.*.method' => 'required|string|max:50',
            'paymentHistory.*.description' => 'nullable|string',
            'paymentHistory.*.createdDate' => 'required|date',
            'paymentHistory.*.createdBy' => 'required|string|max:100',
            'paymentHistory.*.lastModifiedBy' => 'required|string|max:100',
            'paymentHistory.*.lastModifiedDate' => 'required|date',
            'paymentHistory.*.active' => 'required|boolean',
        ];
    }
}
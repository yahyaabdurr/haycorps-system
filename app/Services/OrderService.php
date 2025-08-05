<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function processOrder(array $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            // Process Customer
            $customer = Customer::updateOrCreate(
                ['sk_customer' => $orderData['customer']['skCustomer']],
                $this->mapCustomerData($orderData['customer'])
            );

            // Process Order
            $order = Order::updateOrCreate(
                ['sk_order' => $orderData['skOrder']],
                $this->mapOrderData($orderData, $customer->sk_customer)
            );

            // Process Order Items
            $this->processOrderItems($orderData['orderItems'], $order->sk_order);

            // Process Payments if exists
            if (!empty($orderData['paymentHistory'])) {
                $this->processPayments($orderData['paymentHistory'], $order->sk_order);
            }

            return $order->load('customer', 'orderItems', 'payments');
        });
    }

    private function mapCustomerData(array $customerData): array
    {
        return [
            'customer_id' => $customerData['customerId'],
            'customer_name' => $customerData['customerName'],
            'email' => $customerData['email'],
            'phone_number' => $customerData['phoneNumber'],
            'address1' => $customerData['address1'],
            'address2' => $customerData['address2'],
            'institution' => $customerData['institution'],
            'active' => $customerData['active'],
            'created_by' => $customerData['createdBy'],
            'last_modified_by' => $customerData['lastModifiedBy'],
            'created_at' => $customerData['createdDate'],
            'updated_at' => $customerData['lastModifiedDate'],
        ];
    }

    private function mapOrderData(array $orderData, string $customerId): array
    {
        return [
            'order_id' => $orderData['orderId'],
            'file_url' => $orderData['fileUrl'],
            'pic_employee' => $orderData['picEmployee'],
            'order_date' => $orderData['orderDate'],
            'completion_date' => $orderData['completionDate'],
            'order_status' => $orderData['orderStatus'],
            'total_price' => $orderData['totalPrice'],
            'active' => $orderData['active'],
            'customer_id' => $customerId,
            'last_modified_by' => $orderData['lastModifiedBy'],
            'created_by' => $orderData['createdBy'],
            'created_at' => $orderData['createdDate'],
            'updated_at' => $orderData['lastModifiedDate'],
        ];
    }

    private function processOrderItems(array $orderItems, string $orderId): void
    {
        foreach ($orderItems as $item) {
            OrderItem::updateOrCreate(
                ['sk_order_details' => $item['skOrderDetails']],
                [
                    'order_id' => $orderId,
                    'item_name' => $item['itemName'],
                    'description' => $item['description'],
                    'item_price' => $item['itemPrice'],
                    'number_of_item' => $item['numberOfItem'],
                    'active' => $item['active'],
                    'created_by' => $item['createdBy'],
                    'last_modified_by' => $item['lastModifiedBy'],
                    'created_at' => $item['createdDate'],
                    'updated_at' => $item['lastModifiedDate'],
                ]
            );
        }
    }

    private function processPayments(array $payments, string $orderId): void
    {
        foreach ($payments as $payment) {
            Payment::updateOrCreate(
                ['sk_transaction' => $payment['skTransaction']],
                [
                    'order_id' => $orderId,
                    'transaction_id' => $payment['transactionId'],
                    'type' => $payment['type'],
                    'amount' => $payment['amount'],
                    'method' => $payment['method'],
                    'description' => $payment['description'],
                    'active' => $payment['active'],
                    'created_by' => $payment['createdBy'],
                    'last_modified_by' => $payment['lastModifiedBy'],
                    'created_at' => $payment['createdDate'],
                    'updated_at' => $payment['lastModifiedDate'],
                ]
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SaveOrderRequest;
use App\Http\Resources\Api\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
    }

    public function saveOrder(SaveOrderRequest $request): JsonResponse
    {
        $orderData = $request->validated();
        
        try {
            $order = $this->orderService->processOrder($orderData);
            
            return response()->json([
                'success' => true,
                'message' => 'Order saved successfully',
                'data' => new OrderResource($order)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
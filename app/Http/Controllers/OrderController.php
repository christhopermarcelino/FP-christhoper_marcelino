<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())->get();

        $orders->map(function ($order) {
            $order->customer_name = $order->user->name;
            $order->customer_address = $order->user->address;
            data_forget($order, 'user');
            data_forget($order, 'user_id');
        });

        return $this->sendResponse('Orders retrieved successfully', $orders);
    }

    public function store(OrderRequest $request)
    {
        $data = $request->validated();

        $product = Product::find($data['product_id']);
        if(!$product) {
            return $this->sendError('Product not found', null, 404);
        }

        $total_price = $data['quantity'] * $product->price;

        $data['user_id'] = Auth::id();
        $data['total_price'] = $total_price;

        try {
            $order = Order::create($data);
        } catch(Exception $_) {
            return $this->sendError('Order failed to create', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse('Order created successfully', $order);
    }

    public function show($id)
    {
        $order = Order::find($id);

        if(!$order) {
            return $this->sendError('Order not found', null, 404);
        }
        if($order->user_id != Auth::id()) {
            return $this->sendError('Unauthorized', null, 401);
        }

        $order->customer_name = $order->user->name;
        $order->customer_address = $order->user->address;
        data_forget($order, 'user');
        data_forget($order, 'user_id');

        return $this->sendResponse('Order retrieved successfully', $order);
    }

    public function update(OrderRequest $request, $id)
    {
        $data = $request->validated();

        $order = Order::find($id);
        if(!$order) {
            return $this->sendError('Order not found', null, 404);
        }
        if($order->user_id != Auth::id()) {
            return $this->sendError('Unauthorized', null, 401);
        }

        $product = Product::find($data['product_id']);
        if(!$product) {
            return $this->sendError('Product not found', null, 404);
        }

        $data['total_price'] = $data['quantity'] * $product->price;

        try {
            $order->update($data);
        } catch(Exception $_) {
            return $this->sendError('Order failed to update', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return $this->sendResponse('Order updated successfully', $order);
    }

    public function destroy($id)
    {
        $order = Order::find($id);

        if(!$order) {
            return $this->sendError('Order not found', null, 404);
        }
        if($order->user_id != Auth::id()) {
            return $this->sendError('Unauthorized', null, 401);
        }

        try {
            $order->delete();
        } catch(Exception $_) {
            return $this->sendError('Order failed to delete', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse('Order deleted successfully');
    }

    public function report()
    {
        $orders = Order::all();

        $total_revenue = 0;
        $orders->map(function ($order) use(&$total_revenue) {
            $order->customer_name = $order->user->name;
            $order->customer_address = $order->user->address;
            $order->product_name = $order->product->name;
            $order->category_name = $order->product->category->name;
            $order->order_date = $order->created_at;

            $total_revenue += $order->total_price;

            data_forget($order, 'user');
            data_forget($order, 'user_id');
            data_forget($order, 'product');
            data_forget($order, 'product_id');
            data_forget($order, 'created_at');
            data_forget($order, 'updated_at');
        });

        $response['total_orders'] = $orders->count();
        $response['total_revenue'] = round($total_revenue, 2);
        $response['orders'] = $orders;
        
        return $this->sendResponse('Orders report generated successfully', $response);
    }
}

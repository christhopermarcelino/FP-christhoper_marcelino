<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();

        return $this->sendResponse('Products retrieved successfully', $products);
    }

    public function store(ProductRequest $request)
    {
        $category = Category::find($request->category_id);
        if(!$category) {
            return $this->sendError('Category not found', null, JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $product = Product::create($request->validated());
        } catch(Exception $_) {
            return $this->sendError('Product failed to create', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse('Product created successfully', $product);
    }

    public function show($id)
    {
        $product = Product::with('category')->find($id);
        if(!$product) {
            return $this->sendError('Product not found', null, JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->sendResponse('Product retrieved successfully', $product);
    }

    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        if(!$product) {
            return $this->sendError('Product not found', null, JsonResponse::HTTP_NOT_FOUND);
        }

        $category = Category::find($request->category_id);
        if(!$category) {
            return $this->sendError('Category not found', null, JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $product->update($request->validated());
        } catch(Exception $_) {
            return $this->sendError('Product failed to update', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse('Product updated successfully', $product);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if(!$product) {
            return $this->sendError('Product not found', null, JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $product->delete();
        } catch(QueryException $e) {
            if($e->getCode() == '23503') {
                return $this->sendError('Product cannot be deleted because it is linked to other data', null, JsonResponse::HTTP_CONFLICT);
            }
        } catch(Exception $_) {
            return $this->sendError('Product failed to delete', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse('Product deleted successfully');
    }
}

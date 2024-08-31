<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return $this->sendResponse('Categories retrieved successfully', $categories);
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        if(!$category) {
            return $this->sendError('Category failed to create', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse('Category created successfully', $category);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if(!$category) {
            return $this->sendError('Category not found', null, JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->sendResponse('Category retrieved successfully', $category);
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);

        if(!$category) {
            return $this->sendError('Category not found', null, JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $category->update($request->validated());
        } catch(Exception $_) {
            return $this->sendError('Category failed to update', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse('Category updated successfully', $category);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if(!$category) {
            return $this->sendError('Category not found', null, JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            $category->delete();
        } catch(Exception $_) {
            return $this->sendError('Category failed to delete', null, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse('Category deleted successfully');
    }
}

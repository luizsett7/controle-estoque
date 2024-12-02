<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('code')) {
            $query->where('code', 'like', '%' . $request->input('code') . '%');
        }

        $query->orderBy('name');

        $products = $query->get();

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->expiration_date = $product->expiration_date->format('Y-m-d');

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'cost_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'min_stock' => 'required|integer',
            'stock' => 'required|integer',
            'expiration_date' => 'nullable|date',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:products,code,' . $id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'cost_price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'min_stock' => 'required|integer',
            'stock' => 'required|integer',
            'expiration_date' => 'nullable|date',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function lowStock()
    {
        $products = Product::whereColumn('stock', '<', 'min_stock')
            ->orderBy('stock', 'asc')
            ->get();

        return response()->json($products);
    }

    public function getExpiringProducts()
    {
        $products = Product::where('expiration_date', '<', now()->addDays(7))
            ->orderBy('expiration_date', 'asc')
            ->get()
            ->map(function ($product) {
                $product->expiration_date = Carbon::parse($product->expiration_date)->format('d/m/Y');
                return $product;
            });

        return response()->json($products);
    }
}

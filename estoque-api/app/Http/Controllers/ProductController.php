<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Retrieve all products
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    // Retrieve a single product by ID
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    // Store a new product
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
            'expiry_date' => 'nullable|date',
        ]);

        $product = Product::create($validated);

        return response()->json($product, 201);
    }

    // Update an existing product
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
            'expiry_date' => 'nullable|date',
        ]);

        $product->update($validated);

        return response()->json($product);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    // Get products with low stock
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
            ->get();

        return response()->json($products);
    }
}

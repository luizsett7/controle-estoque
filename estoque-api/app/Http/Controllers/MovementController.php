<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entry,devolution,exit,loss',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
            'reason' => 'required|string|max:255',
        ]);

        $validated['user_id'] = Auth::id();

        $movement = Movement::create($validated);

        $this->updateProductStock($validated['product_id'], $validated['type'], $validated['quantity']);

        return response()->json($movement, 201);
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'user') {
            $movements = Movement::with('product', 'user')
                ->where('user_id', $user->id)
                ->get();
        } else {
            $movements = Movement::with('product', 'user')->get();
        }

        return response()->json($movements);
    }

    public function update(Request $request, $id)
    {
        $movement = Movement::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entry,devolution,exit,loss',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
            'reason' => 'required|string|max:255',
        ]);

        $this->updateProductStock(
            $movement->product_id, 
            in_array($movement->type, ['entry', 'devolution']) ? 'exit' : 'entry', 
            $movement->quantity
        );

        $validated['user_id'] = Auth::id();

        $movement->update($validated);

        $this->updateProductStock($validated['product_id'], $validated['type'], $validated['quantity']);

        return response()->json(['message' => 'Movimento atualizado com sucesso', 'movement' => $movement]);
    }

    public function destroy($id)
    {
        $movement = Movement::findOrFail($id);

        $this->updateProductStock(
            $movement->product_id, 
            in_array($movement->type, ['entry', 'devolution']) ? 'exit' : 'entry', 
            $movement->quantity
        );

        $movement->delete();

        return response()->json(['message' => 'Movimento excluído com sucesso']);
    }

    private function updateProductStock($productId, $type, $quantity)
    {
        $product = Product::findOrFail($productId);

        if ($type === 'entry' || $type === 'devolution') {
            $product->stock += $quantity;
        } else {
            if ($product->stock < $quantity) {
                return;  
            }
            $product->stock -= $quantity;
        }

        $product->save();

        return response()->json([
            'message' => 'Estoque atualizado com sucesso',
            'product' => $product,
        ], 200);  
    }
    public function getStockSummary()
    {
        $totalStock = Product::sum('stock');

        $stockByCategory = Product::selectRaw('categories.name as category_name, SUM(products.stock) as total_stock')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->orderBy('total_stock', 'desc')
            ->get();

        $stockBySupplier = Product::selectRaw('suppliers.name as supplier_name, SUM(products.stock) as total_stock')
            ->join('suppliers', 'products.supplier_id', '=', 'suppliers.id')
            ->groupBy('suppliers.name')
            ->orderBy('total_stock', 'desc')
            ->get();

        return response()->json([
            'total_stock' => $totalStock,
            'stock_by_category' => $stockByCategory,
            'stock_by_supplier' => $stockBySupplier,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateItemRequest;
use App\Http\Requests\StoreItemRequest;
use App\Models\ShoppingList;
use App\Models\Item;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    public function store(StoreItemRequest $request, ShoppingList $list): JsonResponse
    {
        $item = $list->items()->create([
            'name' => $request->input('name'),
            'category' => $request->input('category'),
            'quantity' => $request->input('quantity'),
            'notes' => $request->input('notes'),
        ]);

        return response()->json([
            'item' => $item,
        ], 201);
    }

    public function update(UpdateItemRequest $request, ShoppingList $list, Item $item): JsonResponse
    {
        $item->update($request->only(['name', 'category', 'quantity', 'notes']));

        return response()->json([
            'item' => $item,
        ]);
    }

    public function purchase(ShoppingList $list, Item $item): JsonResponse
    {
        $item->update([
            'purchased' => true,
        ]);

        return response()->json([
            'item' => $item,
        ]);
    }

    public function destroy(ShoppingList $list, Item $item): JsonResponse
    {
        $item->delete();

        return response()->json([
            'mensagem' => 'Item deletado com sucesso!',
        ]);
    }
}

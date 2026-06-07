<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListRequest;
use Illuminate\Http\Request;
use App\Models\ShoppingList;
use Illuminate\Http\JsonResponse;

class ShoppingListController extends Controller
{
    public function store(StoreListRequest $request): JsonResponse
    {
        $list = ShoppingList::create([
            'user_id' => $request->input('user_id'),
            'month' => $request->input('month'),
        ]);

        return response()->json([
            'lista' => $list,
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $user_id = $request->input('user_id');

        $lists = ShoppingList::where('user_id', $user_id)->get();

        return response()->json([
            'listas' => $lists,
        ]);
    }

    public function show(ShoppingList $list): JsonResponse
    {
        $list->load('items');

        return response()->json([
            'lista' => $list,
        ]);
    }

    public function autoFill(ShoppingList $list): JsonResponse
    {
        $previousList = ShoppingList::with('items')
            ->where('user_id', $list->user_id)
            ->where('id', '!=', $list->id)
            ->latest()
            ->first();

        if (!$previousList) {
            return response()->json([
                'message' => 'Nenhuma lista anterior encontrada',
            ], 404);
        }

        foreach ($previousList->items as $item) {
            $list->items()->create([
                'name' => $item->name,
                'category' => $item->category,
                'quantity' => $item->quantity,
                'purchased' => false,
                'notes' => $item->notes,
            ]);
        }

        return response()->json([
            'message' => 'Itens copiados com sucesso',
        ]);
    }
}

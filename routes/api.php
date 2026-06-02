<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShoppingListController;
use App\Http\Controllers\ItemController;

Route::middleware('auth.token')->group(function () {
    Route::post('/lists', [ShoppingListController::class, 'store']);
    Route::get('/lists', [ShoppingListController::class, 'index']);
    Route::get('/lists/{list}', [ShoppingListController::class, 'show']);
    Route::post('/lists/{list}/items', [ItemController::class, 'store']);
    Route::put('/lists/{list}/items/{item}', [ItemController::class, 'update']);
    Route::patch('/lists/{list}/items/{item}/purchase', [ItemController::class, 'purchase']);
    Route::delete('/lists/{list}/items/{item}', [ItemController::class, 'destroy']);
    Route::post('/lists/{list}/auto-fill', [ShoppingListController::class, 'autoFill']);
});

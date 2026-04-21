<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Resourceルートを使うと、CRUDに必要な7つのルートを1行で定義できます
Route::apiResource('users', UserController::class);
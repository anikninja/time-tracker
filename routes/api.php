<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')
    ->group(function () {

        // test routes for guest users
        Route::get('/public', function () {
            return response()->json(['message' => 'This is a public endpoint']);
        });
    });


Route::middleware('auth:sanctum')
    ->group(function () {
        Route::get('/user', function (Request $request) {
            return response()->json([
                'user' => $request->user(),
            ]);
        });
    });

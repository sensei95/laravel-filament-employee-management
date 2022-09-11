<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/employees', function (){
   $employees = \App\Models\Employee::orderBy('last_name')->get();
   return \App\Http\Resources\EmployeeResource::collection($employees);
});

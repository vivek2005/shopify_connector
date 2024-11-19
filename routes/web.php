<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;


Route::get('/', function () {
    return view('welcome');
});


Route::post('/postdata', [AjaxController::class, 'postdata'])->name('postdata');

Route::get('/findmainitemandvariants', [AjaxController::class, 'findmainitemandvariants'])->name('findmainitemandvariants');
Route::get('/findvariants', [AjaxController::class, 'findvariants'])->name('findvariants');

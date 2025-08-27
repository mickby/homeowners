<?php

use App\Http\Controllers\HomeownerParserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('homeowner-parser');
})->name('home');

Route::post('/', HomeownerParserController::class)->name('parse');

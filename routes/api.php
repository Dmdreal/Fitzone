<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/trainers/nearby', [\App\Http\Controllers\Api\TrainerController::class, 'nearby']);

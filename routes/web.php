<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomeController::class, 'home']);

Route::post('/game', [GameController::class, 'newGame']);
Route::get('/game/{gameId}', [GameController::class, 'game']);
Route::put('/game/{gameId}', [GameController::class, 'moveGame']);

Route::get('/room/{gameId}', [RoomController::class, 'room']);
Route::post('/room/cells/{gameId}', [RoomController::class, 'roomCells']);

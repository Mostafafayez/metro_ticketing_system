<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\UserController;




//user
Route::post('/signup', [UserController::class, 'signup']);
Route::post('/login', [UserController::class, 'login']);
Route::put('/users/{id}/update-password', [UserController::class, 'updatePassword']);


// Tickets
Route::middleware('auth:sanctum')->prefix('tickets')->group(function () {
Route::post('/', [TicketController::class, 'addTicket']);
Route::get('/', [TicketController::class, 'getAllTickets']);
Route::delete('/{id}', [TicketController::class, 'deleteTicket']);
Route::post('/{id}', [TicketController::class, 'updateTicket']);
});


// user-Tickets

Route::middleware('auth:sanctum')->prefix('tickets')->group(function () {
    Route::post('/buy', [TicketController::class, 'buyTicket']);
    Route::get('/user', [TicketController::class, 'getTicketsByUser']);
    Route::get('/all/users', [TicketController::class, 'getTickets']);
    Route::get('/approve-status/{ticket_user_id}', [TicketController::class, 'approveStatus']);
    Route::delete('/user/{ticket_user_id}', [TicketController::class, 'deleteUserTicket']);
});


// Branches
Route::middleware('auth:sanctum')->prefix('branches')->group(function () {
Route::get('/', [BranchController::class, 'index']);
Route::post('/', [BranchController::class, 'store']);
});



// Cards
Route::middleware('auth:sanctum')->prefix('cards')->group(function () {
    Route::post('/', [CardController::class, 'store']);
    Route::get('/', [CardController::class, 'index']);
    Route::get('/user', [CardController::class, 'show']);
    Route::post('/{id}', [CardController::class, 'update']);
    Route::delete('/{id}', [CardController::class, 'destroy']);
    Route::post('/renew/user', [CardController::class, 'renew']);
    Route::post('/check-expiry', [CardController::class, 'checkExpiresAtByToken']);
    Route::get('/check-expiry-all', [CardController::class, 'checkExpiresAtForAll']);
});

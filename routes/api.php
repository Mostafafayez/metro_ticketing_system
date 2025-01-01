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

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/tickets/buy', [TicketController::class, 'buyTicket']);
    Route::post('/tickets', [TicketController::class, 'addTicket']);
    Route::get('/tickets', [TicketController::class, 'getAllTickets']);
    Route::get('/tickets/user', [TicketController::class, 'getTicketsByUser']);
    Route::get('/tickets/all/users', [TicketController::class, 'getTickets']);
    Route::delete('/tickets/{id}', [TicketController::class, 'deleteTicket']);
    Route::post('/tickets/{id}', [TicketController::class, 'updateTicket']);

    Route::post('/tickets/status/{ticketId}', [TicketController::class, 'updateTicketStatus']);
    Route::delete('/tickets/user/{ticketId}', [TicketController::class, 'deleteUserTicket']);
});


// Branches
Route::get('/branches', [BranchController::class, 'index']);
Route::post('/branches', [BranchController::class, 'store']);

// Cards
// Route::get('/cards/{userId}', [CardController::class, 'show']);
Route::post('/cards/renew-subscription', [CardController::class, 'renewSubscription']);





// Add subscription
Route::post('/cards/add-subscription', [CardController::class, 'addSubscription']);

// Get profit
Route::get('/profit', [ProfitController::class, 'getProfit']);




Route::prefix('cards')->group(function () {
    Route::post('/', [CardController::class, 'store']);
    Route::get('/', [CardController::class, 'index']);
    Route::get('/user', [CardController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/{id}', [CardController::class, 'update']);
    Route::delete('/{id}', [CardController::class, 'destroy']);
    Route::post('/renew/user', [CardController::class, 'renew'])->middleware('auth:sanctum');
    Route::post('/check-expiry', [CardController::class, 'checkExpiresAtByToken']);
    Route::get('/check-expiry-all', [CardController::class, 'checkExpiresAtForAll']);
});

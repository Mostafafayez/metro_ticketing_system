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
    Route::post('/tickets', [TicketController::class, 'addTicket']);
    Route::get('/tickets', [TicketController::class, 'getAllTickets']);
    Route::get('/tickets/user', [TicketController::class, 'getTicketsByUser']);
    Route::delete('/tickets/{id}', [TicketController::class, 'deleteTicket']);
    Route::post('/tickets/{id}', [TicketController::class, 'updateTicket']);
    Route::post('/tickets/buy', [TicketController::class, 'buyTicket']);
    Route::put('/tickets/status/{ticketId}', [TicketController::class, 'updateTicketStatus']);
    Route::delete('/tickets/user/{ticketId}', [TicketController::class, 'deleteUserTicket']);
});


// Branches
Route::get('/branches', [BranchController::class, 'index']);
Route::post('/branches', [BranchController::class, 'store']);

// Cards
Route::get('/cards/{userId}', [CardController::class, 'show']);
Route::post('/cards/renew-subscription', [CardController::class, 'renewSubscription']);





// Add subscription
Route::post('/cards/add-subscription', [CardController::class, 'addSubscription']);

// Get profit
Route::get('/profit', [ProfitController::class, 'getProfit']);

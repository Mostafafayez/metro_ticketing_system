<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Payment;
use Illuminate\Http\Request;

class CardController extends Controller
{
    // View user's card details
    public function show($userId)
    {
        $card = Card::where('user_id', $userId)->first();
        if (!$card) {
            return response()->json(['error' => 'Card not found'], 404);
        }

        return response()->json($card);
    }

    // Renew subscription
    public function renewSubscription(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_tickets' => 'required|integer|min:1',
            'expires_at' => 'required|date|after:today',
            'method' => 'required|string',
        ]);

        $renewalPrice = 100; // Example fixed price for renewal

        $card = Card::where('user_id', $request->user_id)->where('type', 'subscription')->first();
        if (!$card) {
            return response()->json(['error' => 'Subscription card not found'], 404);
        }

        $card->remaining_tickets += $request->new_tickets;
        $card->expires_at = $request->expires_at;
        $card->save();

        // Record Payment
        $payment = Payment::create([
            'user_id' => $request->user_id,
            'payable_id' => $card->id,
            'payable_type' => Card::class,
            'method' => $request->method,
            'amount' => $renewalPrice,
        ]);

        return response()->json(['card' => $card, 'payment' => $payment]);
    }

}

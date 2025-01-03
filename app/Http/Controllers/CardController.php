<?php
namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
class CardController extends Controller
{


    // Add a new card
    public function store(Request $request)
    {
        $request->validate([
            'national_id' => 'required|string|exists:users,national_id',
            'type' => 'required|in:wallet,subscription',
            'remaining_tickets' => 'nullable|integer',
            'balance' => 'nullable|numeric',
            'expires_at' => 'nullable|date',
        ]);

        // Find the user by national ID
        $user = User::where('national_id', $request->national_id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Check if a card already exists for the user
        $existingCard = Card::where('user_id', $user->id)->first();

        if ($existingCard) {
            return response()->json(['message' => 'User already has a card.'], 422);
        }

        // Create the card
        $card = Card::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'remaining_tickets' => $request->remaining_tickets,
            'balance' => $request->balance,
            'expires_at' => $request->expires_at,
        ]);

        return response()->json(['message' => 'Card created successfully', 'card' => $card], 201);
    }

    // Get all cards with relations
    public function index()
    {
        $cards = Card::with('user')->get();
        return response()->json($cards);
    }

    // Get a card by ID with relations
    public function show()
    {
        $user = Auth::user();
        $card = Card::where('user_id',$user->id)->get();

        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        return response()->json($card);
    }

    // Update a card by ID
    public function update(Request $request, $id)
    {
        $card = Card::find($id);

        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        $request->validate([
            'type' => 'nullable|in:wallet,subscription',
            'remaining_tickets' => 'nullable|integer',
            'balance' => 'nullable|numeric',
            'expires_at' => 'nullable|date',
        ]);

        $card->update($request->all());

        return response()->json(['message' => 'Card updated successfully', 'card' => $card]);
    }

    // Delete a card by ID
    public function destroy($id)
    {
        $card = Card::find($id);

        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        $card->delete();

        return response()->json(['message' => 'Card deleted successfully']);
    }

    // Renew subscription or wallet
    public function renew(Request $request)
    {
        $user = Auth::user();
        $card = Card::where('user_id', $user->id)->first();

        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }

        if ($card->type === 'wallet') {
            $request->validate([
                'remaining_tickets' => 'required|integer|min:1',
            ]);

            $card->remaining_tickets += $request->remaining_tickets;
        } elseif ($card->type === 'subscription') {
            $request->validate([
                'expires_at' => 'required|date',
            ]);

            $card->expires_at = $request->expires_at;
        } else {
            return response()->json(['message' => 'Invalid card type'], 400);
        }

        $card->save();

        return response()->json(['message' => 'Card renewed successfully', 'card' => $card]);
    }

    // Check expiration for a specific card using token


    // Check expiration for all users
    public function checkExpiresAtForAll()
    {
        $cards = Card::with('user')->get(['id', 'user_id', 'expires_at']);

        return response()->json($cards);
    }
}


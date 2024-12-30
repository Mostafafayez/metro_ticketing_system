<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Card;
use Illuminate\Http\Request;

class ProfitController extends Controller
{
    // Get total profit
    public function getProfit(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        // Fetch tickets sold and calculate their profit
        $ticketProfitQuery = Ticket::query();
        if ($from) {
            $ticketProfitQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $ticketProfitQuery->whereDate('created_at', '<=', $to);
        }
        $ticketProfit = $ticketProfitQuery->sum('price');

        // Fetch card renewals and calculate their profit (if wallet or subscription)
        $cardProfitQuery = Card::query()->where('type', '!=', null);
        if ($from) {
            $cardProfitQuery->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $cardProfitQuery->whereDate('created_at', '<=', $to);
        }
        $cardProfit = $cardProfitQuery->sum('price');

        $totalProfit = $ticketProfit + $cardProfit;

        return response()->json([
            'ticket_profit' => $ticketProfit,
            'card_profit' => $cardProfit,
            'total_profit' => $totalProfit,
        ]);
    }
}

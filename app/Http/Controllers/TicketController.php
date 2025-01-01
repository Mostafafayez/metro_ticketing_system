<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketController extends Controller
{
    /**
     * Add a new ticket.
     */
    public function addTicket(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stations_count' => 'required|integer',
            'price' => 'required|numeric',
            // 'image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        // $imagePath = $request->file('image')->store('tickets', 'public');

        $ticket = Ticket::create([
            'name' => $validated['name'],
            'stations_count' => $validated['stations_count'],
            'price' => $validated['price'],
            // 'image' => $imagePath,
        ]);

        return response()->json(['message' => 'Ticket created successfully', 'ticket' => $ticket], 201);
    }

    /**
     * Get all tickets.
     */
    public function getAllTickets()
    {
        $tickets = Ticket::all();
        return response()->json($tickets);
    }

    /**
     * Get tickets by user (using token).
     */
    public function getTicketsByUser()
    {
        $user = Auth::user();
        $tickets = $user->tickets;
        return response()->json($tickets);
    }

    public function getTickets()
    {
        $ticket = Ticket::with('user')->get();

        return response()->json($ticket);
    }



    /**
     * Delete a ticket by ID.
     */
    public function deleteTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }

    /**
     * Update a ticket by ID.
     */
    public function updateTicket(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'stations_count' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
            'image' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('tickets', 'public');
            $ticket->update(['image' => $imagePath]);
        }

        $ticket->update($validated);

        return response()->json(['message' => 'Ticket updated successfully', 'ticket' => $ticket]);
    }

    /**
     * Buy a ticket (store in pivot table).
     */
    public function buyTicket(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'count' => 'required|integer|min:1',
            'status_of_payment' => 'required|string|max:255|in:paid,cash_on_delivery',


        ]);

        // try {
        //     $ticket = Ticket::findOrFail($validated['ticket_id']);
        // } catch (ModelNotFoundException $e) {
        //     return response()->json(['message' => 'Ticket not found'], 404);
        // }

        $user = Auth::user();
        $user->tickets()->attach($validated['ticket_id'], [
            'count' => $validated['count'],
            'status_of_payment' => $validated['status_of_payment'],
        ]);

        return response()->json(['message' => 'Ticket purchased successfully']);
    }

    /**
     * Update ticket status in pivot table.
     */
    public function updateTicketStatus(Request $request, $pivotId)
    {
        // Validate the request
        $validated = $request->validate([
            'status_of_received' => 'required|boolean',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Find the pivot record using the pivot table's unique ID
        $affectedRows = $user->tickets()
            ->wherePivot('id', $pivotId) // Target the specific row in the pivot table
            ->updateExistingPivot($pivotId, [
                'status_of_received' => $validated['status_of_received'],
            ]);

        // Check if any row was updated
        if ($affectedRows === 0) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        // Return a success response
        return response()->json(['message' => 'Ticket status updated successfully',$affectedRows]);
    }

    /**
     * Delete a ticket from pivot table.
     */
    public function deleteUserTicket($ticketId)
    {
        $user = Auth::user();
        $user->tickets()->detach($ticketId);

        return response()->json(['message' => 'User ticket deleted successfully']);
    }
}

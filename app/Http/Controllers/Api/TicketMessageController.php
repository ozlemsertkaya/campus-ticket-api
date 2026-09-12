<?php

namespace App\Http\Controllers\Api;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TicketMessageController extends Controller
{
    //Bir bilete ait tüm mesajları listele
    public function index(Ticket $ticket)
    {
        $messages = $ticket->messages()->with('sender')->oldest()->get();
        return response()->json($messages);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $user = $request->user();

        $message = $ticket->messages()->create([
            'message' => $request->message,
            'sender_type' => get_class($user),
            'sender_id' => $user->id,
        ]);
        return response()->json($message->load('sender'), 201);
    }
}

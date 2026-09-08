<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Exceptions\TicketNotOpenException;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\DB;


class TicketService
{
    public function assign(Ticket $ticket, User $user): Ticket
    {
        if ($ticket->status !== 'open') {
            throw new TicketNotOpenException('Bu talep zaten alınmış veya kapalı!');
        }
        $ticket->assigned_to = $user->id;
        $ticket->status = 'in_progress';
        $ticket->save(); //bellekteki değişiklikleri sql komuutna çevirip çalıştırır.

        return $ticket;
    }

    public function resolve(Ticket $ticket): Ticket
    {
        if ($ticket->status !== 'in_progress') {
            throw new TicketNotOpenException('Bu talep şu an çözülebilir değil.');
        }
        $ticket->status = 'resolved';
        $ticket->resolved_at = now();
        $ticket->save();

        return $ticket;
    }
    public function close(Ticket $ticket, User $closedBy): Ticket
    {
        $IsNormalFlow = $ticket->status === 'resolved'; //talep çözülmüşse, kapatmak her zmn geçerli
        $IsAdminShortcut = $closedBy->role === 'admin'; //Kapatan kişi adminse talebin durumu ne olursa olsun kapatabilir.

        if (!$IsNormalFlow && !$IsAdminShortcut) { //agent, resolved olmayan bir talebi kapatmaya çalışıyorsa hata.
            throw new TicketNotOpenException('Bu talep şu an kapatılamaz.');
        }

        $ticket->status = 'closed';
        $ticket->save();


        return $ticket;
    }
    public function addMessage(Ticket $ticket, $sender, string $message): TicketMessage
    {
        if (!in_array($ticket->status, ['in_progress', 'resolved'])) { //bu değer şu listenin içinde var mı?
            throw new TicketNotOpenException('Bu talebe şu an mesaj eklenemez.');
        }
        return $ticket->messages()->create([
            'sender_type' => get_class($sender), //nesnenin hangi sınıftan olduğunu string olarak dönder.
            'sender_id' => $sender->id,
            'message' => $message,

        ]);
    }
    public function create(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $ticket = Ticket::create([
                'customer_id' => $data['customer_id'],
                'category_id' => $data['category_id'],
                'priority_id' => $data['priority_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => 'open',

            ]);

            return $ticket;
        });
    }
}

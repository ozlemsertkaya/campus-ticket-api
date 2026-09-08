<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TicketMessage extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'sender_type', 'sender_id', 'message'];
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
    public function sender(): MorphTo
    {
        return $this->morphTo(); //hangi tabloya bağlı oldğ. sender_type sütunundan okunuyor parametreye grek yok.
    }
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Priority extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'level'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}

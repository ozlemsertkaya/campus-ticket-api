<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;

use Exception;

class TicketNotOpenException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], 422);
    }
}

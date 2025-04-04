<?php

namespace App\Services\Interfaces;

use App\Models\Ticket;
use Illuminate\Pagination\LengthAwarePaginator;

interface TicketServiceInterface
{
    public function createTicket(array $data): Ticket;

    public function getTickets(): LengthAwarePaginator;
}

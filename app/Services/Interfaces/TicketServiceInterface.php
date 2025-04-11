<?php

namespace App\Services\Interfaces;

use App\Models\Ticket;

interface TicketServiceInterface
{
    public function createTicket(array $data): Ticket;

    public function getTickets();

    public function finishTicket(int $ticketID): int;
}

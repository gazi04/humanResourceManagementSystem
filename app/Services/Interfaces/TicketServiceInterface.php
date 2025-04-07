<?php

namespace App\Services\Interfaces;

use App\Models\Ticket;

interface TicketServiceInterface
{
    public function createTicket(array $data): Ticket;

    public function getTickets();

    public function finishTicket(int $ticketID): Ticket;

    public function openTicket(int $ticketID): Ticket;
}

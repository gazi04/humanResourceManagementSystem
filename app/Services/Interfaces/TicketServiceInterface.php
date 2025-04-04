<?php

namespace App\Services\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface TicketServiceInterface
{
    public function createTicket(array $data): void;

    public function getTickets(): LengthAwarePaginator;
}

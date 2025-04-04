<?php

namespace App\Services;

use App\Models\Ticket;
use App\Services\Interfaces\TicketServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TicketService implements TicketServiceInterface
{
    public function createTicket(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $ticket = Ticket::create($data);
            return $ticket;
        });
    }

    public function getTickets(): LengthAwarePaginator
    {
        return DB::transaction(fn(): LengthAwarePaginator => DB::table('tickets as t')
            ->join('employees as e', 't.employeeID', '=', 'e.employeeID')
            ->select([
                't.ticketID',
                't.subject',
                't.description',
                't.status',
                'e.firstName',
                'e.lastName',
                'e.email',
            ])
            ->paginate(10));
    }
}

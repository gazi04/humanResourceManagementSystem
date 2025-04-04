<?php

namespace App\Services;

use App\Models\Ticket;
use App\Services\Interfaces\TicketServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TicketService implements TicketServiceInterface
{
    public function createticket(array $data): void
    {
        ticket::create($data);
    }

    public function getTickets(): LengthAwarePaginator
    {
        return DB::transaction(fn(): LengthAwarePaginator => DB::table('tickets as t')
            ->join('employees as e', 't.employeeID', '=', 'e.employeeID')
            ->select([
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

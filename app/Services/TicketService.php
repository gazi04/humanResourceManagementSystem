<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Ticket;
use App\Notifications\NewTicketNotification;
use App\Services\Interfaces\TicketServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class TicketService implements TicketServiceInterface
{
    private function gettodaytickets()
    {
        return db::transaction(fn() => db::table('tickets as t')
            ->join('employees as e', 't.employeeid', '=', 'e.employeeid')
            ->select([
                't.ticketid',
                't.subject',
                't.description',
                't.status',
                't.created_at',
                'e.firstname',
                'e.lastname',
                'e.email',
            ])
            ->where('t.status', '!=', 'finished')
            /* TODO- DECOMENT THE LINE BELOW */
            /* ->where('t.created_at', today()) */
            /* ->orderby('t.created_at', 'desc') */
            ->get());
    }

    private function getunfinishedpasttickets()
    {
        return db::transaction(fn() => db::table('tickets as t')
            ->join('employees as e', 't.employeeid', '=', 'e.employeeid')
            ->select([
                't.ticketid',
                't.subject',
                't.description',
                't.status',
                't.created_at',
                'e.firstname',
                'e.lastname',
                'e.email',
            ])
            ->where('t.status', '!=', 'finished')
            ->where('t.created_at', '<', today())
            ->orderby('t.created_at', 'desc')
            ->get());
    }

    public function createTicket(array $data): Ticket
    {
        return DB::transaction(function () use ($data) {
            $ticket = Ticket::create($data);
            $admins = Employee::whereHas('employeeRole', function ($query) {
                $query->where('roleID', 1);
            })->get();
            // Notification::send($admins, new NewTicketNotification($ticket));
            return $ticket;
        });
    }

    public function getTickets()
    {
        return DB::transaction(function () {
            $todayTickets = $this->getTodayTickets();
            $unfinishedTickets = $this->getUnfinishedPastTickets();
            return [
                'todayTickets' => $todayTickets,
                'unfinishedTickets' => $unfinishedTickets
            ];
        });
    }

    public function finishTicket(int $ticketID): Ticket
    {
        return DB::transaction(function () use ($ticketID): Ticket {
            $ticket = Ticket::where('ticketID', '=', $ticketID)
                ->update([
                    'status' => 'finished'
                ])
                ->get();
            return $ticket;
        });
    }

    /* TODO- BE CAREFULL WITH THE IMPLEMENTATION OF THIS METHOD IN THE FRONT END PART  */
    public function openTicket(int $ticketID): Ticket
    {
        return DB::transaction(function () use ($ticketID): Ticket {
            $ticket = Ticket::where('ticketID', '=', $ticketID)
                ->update([
                    'status' => 'open'
                ])
                ->get();
            return $ticket;
        });
    }
}

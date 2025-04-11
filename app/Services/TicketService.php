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
        return DB::transaction(fn () => DB::table('tickets as t')
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
        return DB::transaction(fn () => DB::table('tickets as t')
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
            $admins = Employee::whereHas('employeeRole', function ($query): void {
                $query->where('roleID', 1);
            })->get();

            // Notification::send($admins, new NewTicketNotification($ticket));
            return $ticket;
        });
    }

    public function getTickets()
    {
        return DB::transaction(function (): array {
            $todayTickets = $this->getTodayTickets();
            $unfinishedTickets = $this->getUnfinishedPastTickets();

            return [
                'todayTickets' => $todayTickets,
                'unfinishedTickets' => $unfinishedTickets,
                'summary' => $this->getTicketSummary(),
            ];
        });
    }

    public function finishTicket(int $ticketID): int
    {
        return DB::transaction(fn(): int => Ticket::where('ticketID', '=', $ticketID)
            ->update([
                'status' => 'finished',
            ]));
    }

    public function getTicketSummary(): array
    {
        return DB::transaction(fn(): array => [
            'total_tickets' => DB::table('tickets')->count(),
            'new_today' => DB::table('tickets')
                ->whereDate('created_at', today())
                ->where('status', 'closed')
                ->count(),
            'finished_today' => DB::table('tickets')
                ->where('status', 'finished')
                ->whereDate('created_at', today())
                ->count(),
            'finished_tickets' => DB::table('tickets')
                ->where('status', 'finished')
                ->count(),
        ]);
    }
}

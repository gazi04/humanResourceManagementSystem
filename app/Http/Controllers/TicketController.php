<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\ChangeTicketStatusRequest;
use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService) {}

    public function index()
    {
        try {
            $tickets = $this->ticketService->getTickets();
        } catch (\Exception $ex) {
            Log::error("Couldn't get the tickets from the database.",
                ['error' => $ex->getMessage()]);
            return redirect()->route('admin.dashboard')->with('error', 'Ndodhi një gabim gjatë procesit të marrjes së biletave nga baza e të dhënave.');
        }
        /* dd($tickets); */
        return view('Admin.tickets', [
            'tickets' => $tickets['todayTickets'],
            'unfinishedTickets' => $tickets['unfinishedTickets']
        ]);
    }

    public function create(CreateTicketRequest $request)
    {
        try {
            $validated = $request->only('subject', 'description');
            /** @var Employee $user */
            $user = Auth::guard('employee')->user();
            $validated['employeeID'] = $user->employeeID;

            $ticket = $this->ticketService->createTicket($validated);

            if (empty($ticket)) {
                return redirect()->back()->with('error', 'Procesi i krijimit të biletës ka dështuar provo më vonë përsëri.');
            }

            if ($request->is('hr/*')) {
                $route = 'hr.dashboard';
            }
            else if ($request->is('employee/*')) {
                $route = 'employee.dashboard';
            }
            else if ($request->is('manager/*')) {
                $route = 'employee.dashboard';
            }

            return redirect()->route($route)
                ->with('success', 'Bileta u krijua me sukses.');

        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Krijimi i biletës dështoi për shkak të gabimit të bazës së të dhënave.',
                ['error' => config('app.debug') ? $e->getMessage() : 'Database error occurred']);
        } catch (\PDOException $e) {
            Log::error('Problemi i lidhjes me bazën e të dhënave.',
                ['error' => config('app.debug') ? $e->getMessage() : 'Service unavailable']);
        } catch (\Exception $e) {
            Log::error('Krijimi i biletës dështoi.',
                ['error' => config('app.debug') ? $e->getMessage() : 'An error occurred']);
        }
    }

    public function open(ChangeTicketStatusRequest $request)
    {
        try {
            $validated = $request->only('ticketID');

            $this->ticketService->openTicket($validated['ticketID']);

            return redirect()->route('admin.ticket.index');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Krijimi i biletës dështoi për shkak të gabimit të bazës së të dhënave.',
                ['error' => config('app.debug') ? $e->getMessage() : 'Database error occurred']);
        } catch (\PDOException $e) {
            Log::error('Problemi i lidhjes me bazën e të dhënave.',
                ['error' => config('app.debug') ? $e->getMessage() : 'Service unavailable']);
        } catch (\Exception $e) {
            Log::error('Krijimi i biletës dështoi.',
                ['error' => config('app.debug') ? $e->getMessage() : 'An error occurred']);
        }
    }

    public function finish(ChangeTicketStatusRequest $request)
    {
        try {
            $validated = $request->only('ticketID');

            $this->ticketService->finishTicket($validated['ticketID']);

            return redirect()->route('admin.ticket.index');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Krijimi i biletës dështoi për shkak të gabimit të bazës së të dhënave.',
                ['error' => config('app.debug') ? $e->getMessage() : 'Database error occurred']);
        } catch (\PDOException $e) {
            Log::error('Problemi i lidhjes me bazën e të dhënave.',
                ['error' => config('app.debug') ? $e->getMessage() : 'Service unavailable']);
        } catch (\Exception $e) {
            Log::error('Krijimi i biletës dështoi.',
                ['error' => config('app.debug') ? $e->getMessage() : 'An error occurred']);
        }
    }
}

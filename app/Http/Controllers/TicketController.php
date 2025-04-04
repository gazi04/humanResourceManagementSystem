<?php

namespace App\Http\Controllers;

use App\Http\Requests\Ticket\CreateTicketRequest;
use App\Services\TicketService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(protected TicketService $ticketService) {}

    public function show(): View
    {
        $result = $this->ticketService->getTickets();

        return view('Ticket.Show', ['tickets' => $result]);
    }

    public function create(CreateTicketRequest $request)
    {
        try {
            $validated = $request->only('subject', 'description');
            /** @var Employee $user */
            $user = Auth::guard('employee')->user();
            $validated['employeeID'] = $user->employeeID;

            $ticket = $this->ticketService->createTicket($validated);

            switch (true) {
                case request()->is('hr/*'):
                    $view = 'hr.dashboard';

                case request()->is('employee/*'):
                    $view = 'employee.dashboard';

                case request()->is('manager/*'):
                    $view = 'manager.dashboard';
            }

            return redirect()->route($view)
                ->with('success', 'Ticket created successfully');

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

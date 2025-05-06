<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveBalance\AddDaysToBalanceRequest;
use App\Http\Requests\LeaveBalance\DeductDaysToBalanceRequest;
use App\Services\LeaveService;
use Carbon\Carbon;

class LeaveBalanceController extends Controller
{
    public function __construct(protected LeaveService $leaveService) {}

    public function initYearlyBalance()
    {
        try {
            $this->leaveService->initializeYearlyBalances(Carbon::now()->year);

            return redirect()->route('hr.dashboard')->with('success', 'Bilancet e pushimeve u gjeneruan për punonjësit me sukses.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addDaysFromBalance(AddDaysToBalanceRequest $request)
    {
        $validated = $request->only(['leaveBalanceID', 'days']);

        try {
            $this->leaveService->addDays($validated['leaveBalanceID'], $validated['days']);

            return redirect()->route('hr.dashboard')->with('success', 'Bilanci i pushimeve u përditësua me sukses.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deductDaysFromBalance(DeductDaysToBalanceRequest $request)
    {
        $validated = $request->only(['leaveBalanceID', 'days']);

        try {
            $this->leaveService->deductDays($validated['leaveBalanceID'], $validated['days']);

            return redirect()->route('hr.dashboard')->with('success', 'Bilanci i pushimeve u përditësua me sukses.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

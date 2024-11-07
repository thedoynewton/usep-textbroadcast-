<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CreditBalance;

class CreditBalanceController extends Controller
{
    /**
     * Retrieve the credit balance for display.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCreditBalance()
    {
        // Retrieve the balance from the database, default to 0 if not set
        $creditBalance = CreditBalance::first()->balance ?? 0;
        return response()->json(['creditBalance' => $creditBalance]);
    }

    /**
     * Update the credit balance.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCreditBalance(Request $request)
    {
        // Validate the input
        $request->validate([
            'credit_balance' => 'required|integer|min:0',
        ]);

        // Update or create the credit balance record in the database
        CreditBalance::updateOrCreate(
            ['id' => 1], // Assuming a single record in this table
            ['balance' => $request->input('credit_balance')]
        );

        return redirect()->route('app-management.index', ['section' => 'credit-balance'])
                         ->with('success', 'Credit balance updated successfully.');
    }
}

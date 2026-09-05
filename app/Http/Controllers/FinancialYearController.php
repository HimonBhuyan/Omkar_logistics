<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use Illuminate\Http\Request;

class FinancialYearController extends Controller
{
    public function switchYear(Request $request)
    {
        $request->validate([
            'financial_year' => 'required|string',
        ]);

        $year = trim($request->financial_year);

        if ($year !== 'ALL') {
            $exists = FinancialYear::where('year_string', $year)->exists();
            if (!$exists && $year !== '2026-2027' && $year !== '2025-2026') {
                return back()->with('error', 'Invalid financial year selected.');
            }
        }

        session(['financial_year' => $year]);

        return back()->with('success', "Active Financial Year changed to: {$year}");
    }
}

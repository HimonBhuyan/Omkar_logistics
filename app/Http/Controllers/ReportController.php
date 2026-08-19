<?php

namespace App\Http\Controllers;

use App\Models\Bilty;
use App\Models\CityModel;
use App\Models\AccountLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function biltyRegister(Request $request)
    {
        // Fetch locations list
        $cities = CityModel::orderBy('name')->get();
        
        // Fetch consignors / consignees / parties ledgers list from Party model
        $consignors = \App\Models\Party::where('type', 'consignor')->orWhere('type', 'both')->orderBy('name')->get();
        $consignees = \App\Models\Party::where('type', 'consignee')->orWhere('type', 'both')->orderBy('name')->get();
        $parties = \App\Models\Party::orderBy('name')->get();

        // Unique vehicle expense ledgers
        $vehiclesList = AccountLedger::whereIn('under_group', ['Vehicle Expense', 'Oil Expense', 'Transport Expense'])
            ->orderBy('ledger_name')
            ->pluck('ledger_name')
            ->unique();

        // Build query
        $query = Bilty::with(['fromLocation', 'toLocation', 'consignor', 'consignee', 'billingParty']);

        // 1. From and To Location filter
        if ($request->filled('from_location_id')) {
            $fromLoc = DB::table('locations')->where('name', function($q) use ($request) {
                $q->select('name')->from('cities')->where('id', $request->from_location_id);
            })->first();
            if ($fromLoc) {
                $query->where('from_location_id', $fromLoc->id);
            }
        }
        if ($request->filled('to_location_id')) {
            $toLoc = DB::table('locations')->where('name', function($q) use ($request) {
                $q->select('name')->from('cities')->where('id', $request->to_location_id);
            })->first();
            if ($toLoc) {
                $query->where('to_location_id', $toLoc->id);
            }
        }

        // 2. Consignor / Consignee / Party filter
        if ($request->filled('consignor_id') && $request->has('filter_consignor') && $request->consignor_id !== '') {
            $query->where('consignor_id', $request->consignor_id);
        }
        if ($request->filled('consignee_id') && $request->has('filter_consignee') && $request->consignee_id !== '') {
            $query->where('consignee_id', $request->consignee_id);
        }
        if ($request->filled('billing_party_id') && $request->has('filter_party') && $request->billing_party_id !== '') {
            $query->where('billing_party_id', $request->billing_party_id);
        }

        // 3. Date range filters
        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // 4. Vehicle No filter
        if ($request->filled('vehicle_no')) {
            $query->where('vehicle_no', 'like', '%' . $request->vehicle_no . '%');
        }

        // 5. Billing Type MOP filters (Paid, To Pay, T.B.B.)
        $billingTypes = [];
        if ($request->has('mop_paid')) $billingTypes[] = 'Paid';
        if ($request->has('mop_topay')) $billingTypes[] = 'To Pay';
        if ($request->has('mop_tbb')) $billingTypes[] = 'T.B.B.';
        
        if (!empty($billingTypes)) {
            $query->whereIn('billing_type', $billingTypes);
        }

        // 6. Series filter
        if ($request->filled('series')) {
            $query->where('series', 'like', '%' . $request->series . '%');
        }

        // Fetch Bilty data
        $bilties = $query->orderBy('invoice_date', 'desc')->orderBy('bilty_no', 'desc')->get();

        // Calculate aggregate sums for footer
        $totalPaid = 0;
        $totalToPay = 0;
        $totalTbb = 0;
        $totalNetAmt = 0;
        $totalKg = 0;
        $totalFixed = 0;

        foreach ($bilties as $b) {
            $totalNetAmt += floatval($b->net_amount);
            if ($b->billing_type === 'Paid') {
                $totalPaid += floatval($b->net_amount);
            } elseif ($b->billing_type === 'To Pay') {
                $totalToPay += floatval($b->net_amount);
            } elseif ($b->billing_type === 'T.B.B.') {
                $totalTbb += floatval($b->net_amount);
            }

            if ($b->type === 'Transport Name') {
                $totalFixed += floatval($b->total_qty);
            } else {
                $totalKg += floatval($b->total_qty);
            }
        }

        return view('bilty.register', compact(
            'bilties', 'cities', 'consignors', 'consignees', 'parties', 'vehiclesList',
            'totalPaid', 'totalToPay', 'totalTbb', 'totalNetAmt', 'totalKg', 'totalFixed'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AccountLedger;
use App\Models\CityModel;
use App\Models\Bilty;
use App\Models\BiltyItem;
use App\Models\Location;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BiltyController extends Controller
{
    public function create()
    {
        $locations = CityModel::orderBy('name')->get();
        
        $consignors = Party::where('type', 'consignor')->orWhere('type', 'both')->orderBy('name')->get();
        $consignees = Party::where('type', 'consignee')->orWhere('type', 'both')->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        // Pull unique ledger names (which contain vehicle numbers) under Vehicle Expense and Oil Expense
        $vehicles = AccountLedger::whereIn('under_group', ['Vehicle Expense', 'Oil Expense', 'Transport Expense'])
            ->orderBy('ledger_name')
            ->pluck('ledger_name', 'ledger_name')
            ->unique();

        // Calculate next C.N. number for Series '26-27' (starting from 4197)
        $maxBiltyNo = Bilty::where('series', '26-27')->max('bilty_no');
        $nextBiltyNo = ($maxBiltyNo && $maxBiltyNo >= 4196) ? ($maxBiltyNo + 1) : 4197;

        // Calculate next Voucher No
        $lastVoucher = Bilty::orderBy('voucher_no', 'desc')->first();
        $nextVoucherNo = $lastVoucher ? ($lastVoucher->voucher_no + 1) : 1795;

        return view('bilty.create', compact('locations', 'consignors', 'consignees', 'parties', 'vehicles', 'nextBiltyNo', 'nextVoucherNo'));
    }

    public function getPartyDetails($id)
    {
        $party = Party::find($id);
        if (!$party) {
            return response()->json(['error' => 'Party not found'], 404);
        }
        // Normalize fields for Bilty Javascript mapping expects name, mobile, gstin, address
        return response()->json([
            'id' => $party->id,
            'name' => $party->name,
            'mobile' => $party->mobile,
            'gstin' => $party->gstin,
            'address' => $party->address
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'series' => 'nullable|string|max:5',
            'bilty_no' => 'required|integer',
            'invoice_date' => 'required|date',
            'from_location_id' => 'required|exists:cities,id',
            'to_location_id' => 'required|exists:cities,id',
            'consignor_id' => 'required|exists:account_ledgers,id',
            'consignee_id' => 'required|exists:account_ledgers,id',
            'billing_type' => 'required|string|in:Paid,To Pay,T.B.B.',
            'billing_party_id' => 'nullable|exists:account_ledgers,id',
            'vehicle_no' => 'nullable|string|max:50',
            'eway_bill_no' => 'nullable|string|max:50',
            'cn_no' => 'nullable|string|max:50',
            
            // Grid items
            'items' => 'required|array|min:1',
            'items.*.no_of_pkgs' => 'required|integer|min:1',
            'items.*.packing' => 'nullable|string|max:100',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.invoice_no' => 'nullable|string|max:50',
            'items.*.invoice_value' => 'nullable|numeric|min:0',
            'items.*.weight_type' => 'required|string|in:KG,Fixed',
            'items.*.weight_val' => 'nullable|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.st' => 'nullable|numeric|min:0',
            'items.*.rc' => 'nullable|numeric|min:0',
            'items.*.sc' => 'nullable|numeric|min:0',
            'items.*.dd' => 'nullable|numeric|min:0',

            // Summary Totals
            'total_packages' => 'required|integer',
            'total_qty' => 'required|numeric',
            'gross_amount' => 'required|numeric',
            'st_charge' => 'nullable|numeric',
            'rc_charge' => 'nullable|numeric',
            'sc_charge' => 'nullable|numeric',
            'dd_charge' => 'nullable|numeric',
            'round_off' => 'nullable|numeric',
            'net_amount' => 'required|numeric',

            // Payments
            'cash_amount' => 'nullable|numeric',
            'card_amount' => 'nullable|numeric',
            'upi_chq_amount' => 'nullable|numeric',
            'ref_no' => 'nullable|string|max:50',
            'payment_date' => 'nullable|date',
            'bank_account' => 'nullable|string|max:100',
            'balance_amount' => 'required|numeric',
            'remark' => 'nullable|string',
            'voucher_no' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // Locate or seed the target location inside the 'locations' table to satisfy constraints
            $fromCity = CityModel::findOrFail($request->from_location_id);
            $toCity = CityModel::findOrFail($request->to_location_id);

            $fromLoc = Location::firstOrCreate(['name' => $fromCity->name]);
            $toLoc = Location::firstOrCreate(['name' => $toCity->name]);

            // Map consignor/consignee ledger records to seed parties table to satisfy constraints
            $consignorLedger = AccountLedger::findOrFail($request->consignor_id);
            $consigneeLedger = AccountLedger::findOrFail($request->consignee_id);

            // Fetch or seed parties
            $dbConsignor = DB::table('parties')->updateOrInsert(
                ['id' => $consignorLedger->id],
                ['name' => $consignorLedger->ledger_name, 'created_at' => now(), 'updated_at' => now()]
            );
            $dbConsignee = DB::table('parties')->updateOrInsert(
                ['id' => $consigneeLedger->id],
                ['name' => $consigneeLedger->ledger_name, 'created_at' => now(), 'updated_at' => now()]
            );

            if ($request->billing_party_id) {
                $billingLedger = AccountLedger::findOrFail($request->billing_party_id);
                DB::table('parties')->updateOrInsert(
                    ['id' => $billingLedger->id],
                    ['name' => $billingLedger->ledger_name, 'created_at' => now(), 'updated_at' => now()]
                );
            }

            // Create Bilty Header
            $bilty = Bilty::create([
                'series' => $request->series ?? '26-27',
                'bilty_no' => $request->bilty_no,
                'invoice_date' => $request->invoice_date,
                'from_location_id' => $fromLoc->id,
                'to_location_id' => $toLoc->id,
                'consignor_id' => $consignorLedger->id,
                'consignee_id' => $consigneeLedger->id,
                'billing_type' => $request->billing_type,
                'billing_party_id' => $request->billing_party_id,
                'cn_no' => $request->cn_no,
                'vehicle_no' => $request->vehicle_no,
                'eway_bill_no' => $request->eway_bill_no,
                
                'total_packages' => $request->total_packages,
                'total_qty' => $request->total_qty,
                'gross_amount' => $request->gross_amount,
                
                'st_charge' => $request->st_charge ?? 0.00,
                'rc_charge' => $request->rc_charge ?? 0.00,
                'sc_charge' => $request->sc_charge ?? 0.00,
                'dd_charge' => $request->dd_charge ?? 0.00,
                'round_off' => $request->round_off ?? 0.00,
                'net_amount' => $request->net_amount,
                
                'cash_amount' => $request->cash_amount ?? 0.00,
                'card_amount' => $request->card_amount ?? 0.00,
                'upi_chq_amount' => $request->upi_chq_amount ?? 0.00,
                'ref_no' => $request->ref_no,
                'payment_date' => $request->payment_date,
                'bank_account' => $request->bank_account,
                'balance_amount' => $request->balance_amount,
                'remark' => $request->remark,
                'voucher_no' => $request->voucher_no,
            ]);

            // Save Bilty Items
            foreach ($request->items as $itemData) {
                BiltyItem::create([
                    'bilty_id' => $bilty->id,
                    'no_of_pkgs' => $itemData['no_of_pkgs'],
                    'packing' => $itemData['packing'] ?? '',
                    'description' => $itemData['description'] ?? '',
                    'invoice_no' => $itemData['invoice_no'] ?? '',
                    'invoice_value' => $itemData['invoice_value'] ?? 0.00,
                    'weight_type' => $itemData['weight_type'],
                    'weight_val' => $itemData['weight_val'] ?? 0.000,
                    'qty' => $itemData['qty'],
                    'rate' => $itemData['rate'],
                    'st' => $itemData['st'] ?? 0.00,
                    'rc' => $itemData['rc'] ?? 0.00,
                    'sc' => $itemData['sc'] ?? 0.00,
                    'dd' => $itemData['dd'] ?? 0.00,
                ]);
            }

            DB::commit();

            if ($request->has('print_after_save')) {
                return redirect()->route('bilty.print', $bilty->id);
            }

            return redirect()->route('bilty.create')
                ->with('success', 'Bilty #' . $bilty->bilty_no . ' saved successfully!')
                ->with('print_id', $bilty->id);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            $errorCode = $e->errorInfo[1] ?? null;
            if ($errorCode == 1062) {
                // Intercept Duplicate Entry SQLSTATE code
                $errorMessage = "Duplicate Entry: A consignment note (Bilty) with the number '" . $request->bilty_no . "' already exists for series '" . ($request->series ?? '26-27') . "'. Please enter a unique Bilty number.";
            } else {
                $errorMessage = 'Database Error: ' . $e->getMessage();
            }
            return back()
                ->with('error', $errorMessage)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to save Bilty: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function lookup($bilty_no)
    {
        // Try to find the bilty with the current series or fall back to any series
        $bilty = Bilty::with(['items'])->where('bilty_no', $bilty_no)->first();
        if (!$bilty) {
            return response()->json(['error' => 'Bilty consignment not found'], 404);
        }

        // Map model fields to City IDs
        $fromLocationName = DB::table('locations')->where('id', $bilty->from_location_id)->value('name');
        $toLocationName = DB::table('locations')->where('id', $bilty->to_location_id)->value('name');

        $fromCity = DB::table('cities')->where('name', $fromLocationName)->first();
        if (!$fromCity && $fromLocationName) {
            $fromCity = DB::table('cities')->where('name', 'like', $fromLocationName)->first();
        }

        $toCity = DB::table('cities')->where('name', $toLocationName)->first();
        if (!$toCity && $toLocationName) {
            $toCity = DB::table('cities')->where('name', 'like', $toLocationName)->first();
        }

        return response()->json([
            'bilty' => $bilty,
            'from_city_id' => $fromCity ? $fromCity->id : null,
            'to_city_id' => $toCity ? $toCity->id : null,
        ]);
    }

    public function update(Request $request, $id)
    {
        $bilty = Bilty::findOrFail($id);

        $request->validate([
            'series' => 'nullable|string|max:5',
            'bilty_no' => 'required|integer',
            'invoice_date' => 'required|date',
            'from_location_id' => 'required|exists:cities,id',
            'to_location_id' => 'required|exists:cities,id',
            'consignor_id' => 'required|exists:account_ledgers,id',
            'consignee_id' => 'required|exists:account_ledgers,id',
            'billing_type' => 'required|string|in:Paid,To Pay,T.B.B.',
            'billing_party_id' => 'nullable|exists:account_ledgers,id',
            'vehicle_no' => 'nullable|string|max:50',
            'eway_bill_no' => 'nullable|string|max:50',
            'cn_no' => 'nullable|string|max:50',
            
            // Grid items
            'items' => 'required|array|min:1',
            'items.*.no_of_pkgs' => 'required|integer|min:1',
            'items.*.packing' => 'nullable|string|max:100',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.invoice_no' => 'nullable|string|max:50',
            'items.*.invoice_value' => 'nullable|numeric|min:0',
            'items.*.weight_type' => 'required|string|in:KG,Fixed',
            'items.*.weight_val' => 'nullable|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.st' => 'nullable|numeric|min:0',
            'items.*.rc' => 'nullable|numeric|min:0',
            'items.*.sc' => 'nullable|numeric|min:0',
            'items.*.dd' => 'nullable|numeric|min:0',

            // Summary Totals
            'total_packages' => 'required|integer',
            'total_qty' => 'required|numeric',
            'gross_amount' => 'required|numeric',
            'st_charge' => 'nullable|numeric',
            'rc_charge' => 'nullable|numeric',
            'sc_charge' => 'nullable|numeric',
            'dd_charge' => 'nullable|numeric',
            'round_off' => 'nullable|numeric',
            'net_amount' => 'required|numeric',

            // Payments
            'cash_amount' => 'nullable|numeric',
            'card_amount' => 'nullable|numeric',
            'upi_chq_amount' => 'nullable|numeric',
            'ref_no' => 'nullable|string|max:50',
            'payment_date' => 'nullable|date',
            'bank_account' => 'nullable|string|max:100',
            'balance_amount' => 'required|numeric',
            'remark' => 'nullable|string',
            'voucher_no' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            $fromCity = CityModel::findOrFail($request->from_location_id);
            $toCity = CityModel::findOrFail($request->to_location_id);

            $fromLoc = Location::firstOrCreate(['name' => $fromCity->name]);
            $toLoc = Location::firstOrCreate(['name' => $toCity->name]);

            $consignorLedger = AccountLedger::findOrFail($request->consignor_id);
            $consigneeLedger = AccountLedger::findOrFail($request->consignee_id);

            DB::table('parties')->updateOrInsert(
                ['id' => $consignorLedger->id],
                ['name' => $consignorLedger->ledger_name, 'created_at' => now(), 'updated_at' => now()]
            );
            DB::table('parties')->updateOrInsert(
                ['id' => $consigneeLedger->id],
                ['name' => $consigneeLedger->ledger_name, 'created_at' => now(), 'updated_at' => now()]
            );

            if ($request->billing_party_id) {
                $billingLedger = AccountLedger::findOrFail($request->billing_party_id);
                DB::table('parties')->updateOrInsert(
                    ['id' => $billingLedger->id],
                    ['name' => $billingLedger->ledger_name, 'created_at' => now(), 'updated_at' => now()]
                );
            }

            // Update Header
            $bilty->update([
                'series' => $request->series ?? '26-27',
                'bilty_no' => $request->bilty_no,
                'invoice_date' => $request->invoice_date,
                'from_location_id' => $fromLoc->id,
                'to_location_id' => $toLoc->id,
                'consignor_id' => $consignorLedger->id,
                'consignee_id' => $consigneeLedger->id,
                'billing_type' => $request->billing_type,
                'billing_party_id' => $request->billing_party_id,
                'cn_no' => $request->cn_no,
                'vehicle_no' => $request->vehicle_no,
                'eway_bill_no' => $request->eway_bill_no,
                
                'total_packages' => $request->total_packages,
                'total_qty' => $request->total_qty,
                'gross_amount' => $request->gross_amount,
                
                'st_charge' => $request->st_charge ?? 0.00,
                'rc_charge' => $request->rc_charge ?? 0.00,
                'sc_charge' => $request->sc_charge ?? 0.00,
                'dd_charge' => $request->dd_charge ?? 0.00,
                'round_off' => $request->round_off ?? 0.00,
                'net_amount' => $request->net_amount,
                
                'cash_amount' => $request->cash_amount ?? 0.00,
                'card_amount' => $request->card_amount ?? 0.00,
                'upi_chq_amount' => $request->upi_chq_amount ?? 0.00,
                'ref_no' => $request->ref_no,
                'payment_date' => $request->payment_date,
                'bank_account' => $request->bank_account,
                'balance_amount' => $request->balance_amount,
                'remark' => $request->remark,
                'voucher_no' => $request->voucher_no,
            ]);

            // Clear old items and write new ones
            BiltyItem::where('bilty_id', $bilty->id)->delete();

            foreach ($request->items as $itemData) {
                BiltyItem::create([
                    'bilty_id' => $bilty->id,
                    'no_of_pkgs' => $itemData['no_of_pkgs'],
                    'packing' => $itemData['packing'] ?? '',
                    'description' => $itemData['description'] ?? '',
                    'invoice_no' => $itemData['invoice_no'] ?? '',
                    'invoice_value' => $itemData['invoice_value'] ?? 0.00,
                    'weight_type' => $itemData['weight_type'],
                    'weight_val' => $itemData['weight_val'] ?? 0.000,
                    'qty' => $itemData['qty'],
                    'rate' => $itemData['rate'],
                    'st' => $itemData['st'] ?? 0.00,
                    'rc' => $itemData['rc'] ?? 0.00,
                    'sc' => $itemData['sc'] ?? 0.00,
                    'dd' => $itemData['dd'] ?? 0.00,
                ]);
            }

            DB::commit();

            if ($request->has('print_after_save')) {
                return redirect()->route('bilty.print', $bilty->id);
            }

            return redirect()->route('bilty.create')
                ->with('success', 'Bilty #' . $bilty->bilty_no . ' updated successfully!')
                ->with('print_id', $bilty->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to update Bilty: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function print($id)
    {
        $bilty = Bilty::with(['fromLocation', 'toLocation', 'consignor', 'consignee', 'billingParty', 'items'])->findOrFail($id);
        return view('bilty.print', compact('bilty'));
    }
}

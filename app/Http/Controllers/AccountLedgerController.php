<?php

namespace App\Http\Controllers;

use App\Models\AccountLedger;
use App\Models\GroupLedger;
use Illuminate\Http\Request;

class AccountLedgerController extends Controller
{
    public function index()
    {
        $ledgers = AccountLedger::orderBy('ledger_name')->get();
        $groups  = GroupLedger::orderBy('sort_order')->pluck('name');
        $nextCode = (AccountLedger::max('code') ?? 0) + 1;

        // Start with an empty (new) ledger form
        $selected = new AccountLedger([
            'code'          => $nextCode,
            'state'         => 'Assam',
            'country'       => 'INDIA',
            'payment_type'  => 'cash',
            'customer_type' => 'retailer',
            'dom'           => now()->format('Y-m-d'),
            'dob'           => now()->format('Y-m-d'),
        ]);

        return view('account.ledger', compact('ledgers', 'groups', 'selected', 'nextCode'));
    }

    public function load($id)
    {
        $ledgers  = AccountLedger::orderBy('ledger_name')->get();
        $groups   = GroupLedger::orderBy('sort_order')->pluck('name');
        $selected = AccountLedger::findOrFail($id);
        $nextCode = (AccountLedger::max('code') ?? 0) + 1;

        return view('account.ledger', compact('ledgers', 'groups', 'selected', 'nextCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'          => 'required|integer|unique:account_ledgers,code',
            'ledger_name'   => 'nullable|string|max:255',
            'under_group'   => 'nullable|string|max:100',
            'contact_person'=> 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'pin_code'      => 'nullable|string|max:10',
            'phone_o'       => 'nullable|string|max:20',
            'phone_r'       => 'nullable|string|max:20',
            'points'        => 'nullable|numeric',
            'credit_limit'  => 'nullable|numeric',
            'limit_days'    => 'nullable|integer',
            'mobile'        => 'nullable|string|max:20',
            'fax'           => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'salesman'      => 'nullable|string|max:100',
            'print_copy'    => 'nullable|integer',
            'web'           => 'nullable|string|max:255',
            'gst_no'        => 'nullable|string|max:20',
            'di_no'         => 'nullable|string|max:20',
            'transport'     => 'nullable|string|max:100',
            'bank_name'     => 'nullable|string|max:255',
            'account_no'    => 'nullable|string|max:30',
            'ifsc'          => 'nullable|string|max:20',
            'opening'       => 'nullable|numeric',
            'dom'           => 'nullable|date',
            'margin'        => 'nullable|numeric',
            'dob'           => 'nullable|date',
            'discnt'        => 'nullable|numeric',
            'payment_type'  => 'nullable|in:cash,credit',
            'customer_type' => 'nullable|in:retailer,wholesaler',
            // Staff Salary custom inputs
            'voter_card'    => 'nullable|string|max:50',
            'adhar_card'    => 'nullable|string|max:50',
            'pan_card'      => 'nullable|string|max:50',
            'driving_license'=> 'nullable|string|max:50',
            'staff_bank_name'=> 'nullable|string|max:255',
            'staff_account_no'=> 'nullable|string|max:50',
            'staff_ifsc'    => 'nullable|string|max:50',
            'esi_number'    => 'nullable|string|max:50',
            'pf_number'     => 'nullable|string|max:50',
            // Vehicle custom inputs
            'vehicle_gst'   => 'nullable|string|max:50',
            'driver_name'   => 'nullable|string|max:255',
            'driver_phone'  => 'nullable|string|max:20',
        ]);

        // Map staff-specific form inputs to existing database columns
        $mappedData = $data;
        if ($request->has('voter_card')) $mappedData['fax'] = $request->voter_card;
        if ($request->has('adhar_card')) $mappedData['salesman'] = $request->adhar_card;
        if ($request->has('pan_card')) $mappedData['web'] = $request->pan_card;
        if ($request->has('driving_license')) $mappedData['di_no'] = $request->driving_license;
        if ($request->has('staff_bank_name')) $mappedData['bank_name'] = $request->staff_bank_name;
        if ($request->has('staff_account_no')) $mappedData['account_no'] = $request->staff_account_no;
        if ($request->has('staff_ifsc')) $mappedData['ifsc'] = $request->staff_ifsc;
        if ($request->has('esi_number')) $mappedData['phone_o'] = $request->esi_number;
        if ($request->has('pf_number')) $mappedData['phone_r'] = $request->pf_number;
        if ($request->has('vehicle_gst')) $mappedData['gst_no'] = $request->vehicle_gst;
        if ($request->has('driver_name')) $mappedData['contact_person'] = $request->driver_name;
        if ($request->has('driver_phone')) $mappedData['mobile'] = $request->driver_phone;

        $ledger = AccountLedger::create($mappedData);

        return redirect()->route('account.ledger.load', $ledger->id)
            ->with('success', 'Account Ledger saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $ledger = AccountLedger::findOrFail($id);

        $data = $request->validate([
            'code'          => 'required|integer|unique:account_ledgers,code,' . $id,
            'ledger_name'   => 'nullable|string|max:255',
            'under_group'   => 'nullable|string|max:100',
            'contact_person'=> 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'city'          => 'nullable|string|max:100',
            'state'         => 'nullable|string|max:100',
            'country'       => 'nullable|string|max:100',
            'pin_code'      => 'nullable|string|max:10',
            'phone_o'       => 'nullable|string|max:20',
            'phone_r'       => 'nullable|string|max:20',
            'points'        => 'nullable|numeric',
            'credit_limit'  => 'nullable|numeric',
            'limit_days'    => 'nullable|integer',
            'mobile'        => 'nullable|string|max:20',
            'fax'           => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:255',
            'salesman'      => 'nullable|string|max:100',
            'print_copy'    => 'nullable|integer',
            'web'           => 'nullable|string|max:255',
            'gst_no'        => 'nullable|string|max:20',
            'di_no'         => 'nullable|string|max:20',
            'transport'     => 'nullable|string|max:100',
            'bank_name'     => 'nullable|string|max:255',
            'account_no'    => 'nullable|string|max:30',
            'ifsc'          => 'nullable|string|max:20',
            'opening'       => 'nullable|numeric',
            'dom'           => 'nullable|date',
            'margin'        => 'nullable|numeric',
            'dob'           => 'nullable|date',
            'discnt'        => 'nullable|numeric',
            'payment_type'  => 'nullable|in:cash,credit',
            'customer_type' => 'nullable|in:retailer,wholesaler',
            // Staff Salary custom inputs
            'voter_card'    => 'nullable|string|max:50',
            'adhar_card'    => 'nullable|string|max:50',
            'pan_card'      => 'nullable|string|max:50',
            'driving_license'=> 'nullable|string|max:50',
            'staff_bank_name'=> 'nullable|string|max:255',
            'staff_account_no'=> 'nullable|string|max:50',
            'staff_ifsc'    => 'nullable|string|max:50',
            'esi_number'    => 'nullable|string|max:50',
            'pf_number'     => 'nullable|string|max:50',
            // Vehicle custom inputs
            'vehicle_gst'   => 'nullable|string|max:50',
            'driver_name'   => 'nullable|string|max:255',
            'driver_phone'  => 'nullable|string|max:20',
        ]);

        // Map staff-specific form inputs to existing database columns
        $mappedData = $data;
        if ($request->has('voter_card')) $mappedData['fax'] = $request->voter_card;
        if ($request->has('adhar_card')) $mappedData['salesman'] = $request->adhar_card;
        if ($request->has('pan_card')) $mappedData['web'] = $request->pan_card;
        if ($request->has('driving_license')) $mappedData['di_no'] = $request->driving_license;
        if ($request->has('staff_bank_name')) $mappedData['bank_name'] = $request->staff_bank_name;
        if ($request->has('staff_account_no')) $mappedData['account_no'] = $request->staff_account_no;
        if ($request->has('staff_ifsc')) $mappedData['ifsc'] = $request->staff_ifsc;
        if ($request->has('esi_number')) $mappedData['phone_o'] = $request->esi_number;
        if ($request->has('pf_number')) $mappedData['phone_r'] = $request->pf_number;
        if ($request->has('vehicle_gst')) $mappedData['gst_no'] = $request->vehicle_gst;
        if ($request->has('driver_name')) $mappedData['contact_person'] = $request->driver_name;
        if ($request->has('driver_phone')) $mappedData['mobile'] = $request->driver_phone;

        $ledger->update($mappedData);

        return redirect()->route('account.ledger.load', $ledger->id)
            ->with('success', 'Account Ledger updated successfully.');
    }

    public function destroy($id)
    {
        AccountLedger::findOrFail($id)->delete();

        return redirect()->route('account.ledger')
            ->with('success', 'Account Ledger deleted.');
    }
}

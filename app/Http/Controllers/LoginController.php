<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FinancialYear;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $companies = Company::all();

        return view('auth.login', compact('companies'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'company' => 'required',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($request->username);
        
        $user = User::where('username', $username)->first();

        if (!$user || !hash_equals($user->username, $username) || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('username', 'company'));
        }

        if (isset($user->is_active) && !$user->is_active) {
            return back()->withErrors([
                'username' => 'Your account has been deactivated. Please contact administrator.',
            ])->withInput($request->only('username', 'company'));
        }

        // Validate Company Access Authorization
        $selectedCompany = Company::find($request->company);
        if (!$selectedCompany || !$user->canAccessCompany($request->company)) {
            $companyName = $selectedCompany ? $selectedCompany->name : 'selected company';
            return back()->withErrors([
                'username' => "You are not authorized to log in to {$companyName}. Please contact your administrator.",
            ])->withInput($request->only('username', 'company'));
        }

        Auth::login($user);

        // Retrieve default active financial year or first financial year
        $activeFinYear = FinancialYear::where('is_active', true)->first();
        if (!$activeFinYear) {
            $activeFinYear = FinancialYear::first();
        }
        $finYearString = $activeFinYear ? $activeFinYear->year_string : '2026-2027';

        session([
            'company_id' => $selectedCompany->id,
            'company_name' => $selectedCompany->name,
            'financial_year' => session('financial_year', $finYearString),
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard()
    {
        return view('dashboard');
    }
}

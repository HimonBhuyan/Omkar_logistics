<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FinancialYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // If already logged in, redirect to bilty page
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $companies = Company::all();
        $financialYears = FinancialYear::all();

        return view('auth.login', compact('companies', 'financialYears'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'company' => 'required',
            'financial_year' => 'required',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            // Retrieve company name & financial year to store in session
            $company = Company::find($request->company);
            $finYear = FinancialYear::where('year_string', $request->financial_year)->first();

            session([
                'company_id' => $request->company,
                'company_name' => $company ? $company->name : 'OMKAAR LOGISTICS',
                'financial_year' => $request->financial_year,
            ]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username', 'company', 'financial_year'));
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

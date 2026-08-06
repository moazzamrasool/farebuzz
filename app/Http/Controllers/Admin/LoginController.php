<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // Show admin login form
    public function index()
    {
        return view('admin.login');
    }

    // Process admin login — uses 'admin' guard (separate admins table)
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:5',
        ]);

        if ($validator->fails()) {
            return redirect()->route('crm.login')
                ->withInput()
                ->withErrors($validator);
        }

        if (Auth::guard('admin')->attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ])) {
            $admin = Auth::guard('admin')->user();

            if ($admin->status !== 'active' || $admin->isCompanySuspended()) {
                $message = $admin->suspensionMessage();
                Auth::guard('admin')->logout();

                return redirect()->route('crm.login')
                    ->with('error', $message);
            }

            $request->session()->regenerate();
            return redirect()->route('crm.dashboard');
        }

        return redirect()->route('crm.login')
            ->with('error', 'Invalid email or password. Please try again.');
    }

    // Logout admin
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('crm.login');
    }
}

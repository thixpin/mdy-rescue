<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AdminController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $admins = Admin::where('id', '!=', Auth::guard('admin')->id())->get();

        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', Password::defaults()],
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'activation_token' => Str::random(60),
        ]);

        // TODO: Send activation email

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin created successfully. They will need to be activated.');
    }

    public function edit(Admin $admin)
    {
        return view('admin.admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,'.$admin->id],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $admin->update($validated);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deleted successfully.');
    }

    public function activate(Admin $admin)
    {
        if (! $admin->isPendingActivation()) {
            return back()->with('error', 'This admin is not pending activation.');
        }

        $admin->update([
            'activation_status' => 'activated',
            'activated_by' => Auth::guard('admin')->id(),
            'status' => 'active',
            'activation_token' => null,
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin activated successfully.');
    }

    public function deactivate(Admin $admin)
    {
        if ($admin->id === Auth::guard('admin')->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $admin->update([
            'status' => 'inactive',
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin deactivated successfully.');
    }
}

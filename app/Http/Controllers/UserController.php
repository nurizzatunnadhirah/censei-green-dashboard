<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')
            ->paginate(10);

        return view('users.index', compact('users'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
    'required',
    'confirmed',

    Password::min(8)
        ->letters()
        ->mixedCase()
        ->numbers(),
],
            'role' => 'required|in:Admin,Staff',
            'status' => 'required|in:Active,Inactive',
            'phone_number' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
        ]);


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
            'phone_number' => $request->phone_number,
            'position' => $request->position,
        ]);


        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'User added successfully.'
            );
    }


    public function update(
    Request $request,
    User $user
)
{
    $request->validate([
        'name' => 'required|string|max:255',

        'email' =>
            'required|email|max:255|unique:users,email,' .
            $user->id,

        'role' =>
            'required|in:Admin,Staff',

        'status' =>
            'required|in:Active,Inactive',

        'phone_number' =>
            'nullable|string|max:50',

        'position' =>
            'nullable|string|max:255',

        'password' => [
    'nullable',
    'confirmed',

    Password::min(8)
        ->letters()
        ->mixedCase()
        ->numbers(),
],
    ]);


    /*
    |--------------------------------------------------------------------------
    | Prevent Admin From Locking Themselves Out
    |--------------------------------------------------------------------------
    */

    if (auth()->id() === $user->id) {

        if ($request->role !== 'Admin') {

            return redirect()
                ->route('users.index')
                ->with(
                    'error',
                    'You cannot change your own Admin account to Staff.'
                );
        }


        if ($request->status !== 'Active') {

            return redirect()
                ->route('users.index')
                ->with(
                    'error',
                    'You cannot deactivate your own account.'
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Protect Last Active Admin
    |--------------------------------------------------------------------------
    */

    $removingAdminAccess =
        $user->role === 'Admin'
        &&
        $user->status === 'Active'
        &&
        (
            $request->role !== 'Admin'
            ||
            $request->status !== 'Active'
        );


    if ($removingAdminAccess) {

        $activeAdminCount = User::where(
                'role',
                'Admin'
            )
            ->where(
                'status',
                'Active'
            )
            ->count();


        if ($activeAdminCount <= 1) {

            return redirect()
                ->route('users.index')
                ->with(
                    'error',
                    'The last active Admin account cannot be deactivated or changed to Staff.'
                );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Update User
    |--------------------------------------------------------------------------
    */

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'status' => $request->status,
        'phone_number' => $request->phone_number,
        'position' => $request->position,
    ];


    if ($request->filled('password')) {

        $data['password'] =
            Hash::make($request->password);
    }


    $user->update($data);


    return redirect()
        ->route('users.index')
        ->with(
            'success',
            'User updated successfully.'
        );
}
}
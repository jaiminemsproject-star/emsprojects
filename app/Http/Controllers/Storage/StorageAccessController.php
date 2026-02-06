<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StorageAccessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Only super-admins (or whoever has this) can manage access
        $this->middleware('permission:core.access.manage');
    }public function index(Request $request)
{
    $usersQuery = User::query()->with('roles');

    // Filter: Name
    if ($request->filled('name')) {
        $usersQuery->where('name', 'like', '%' . trim($request->name) . '%');
    }

    // Filter: Email
    if ($request->filled('email')) {
        $usersQuery->where('email', 'like', '%' . trim($request->email) . '%');
    }

    // Filter: Role
    if ($request->filled('role')) {
        $usersQuery->whereHas('roles', function ($q) use ($request) {
            $q->where('name', $request->role);
        });
    }

    // Filter: Storage Access
    if ($request->filled('has_storage')) {
        $permission = 'storage.view';

        if ((int) $request->has_storage === 1) {
            $usersQuery->permission($permission);
        } else {
            $usersQuery->whereDoesntHave('permissions', function ($q) use ($permission) {
                $q->where('name', $permission);
            });
        }
    }

    // Pagination (keeps filters)
    $users = $usersQuery
        ->orderBy('name')
        ->paginate(perPage: 10)
        ->withQueryString();

    return view('storage.access.index', [
        'users' => $users,
        'storagePermission' => 'storage.view',
    ]);
}



    public function update(Request $request, User $user)
    {
        $storagePermission = 'storage.view';
        $grant = $request->boolean('grant');

        // grant/revoke only affects DIRECT permissions
        if ($grant) {
            $user->givePermissionTo($storagePermission);
        } else {
            $user->revokePermissionTo($storagePermission);
        }

        return back()->with('success', 'Storage access updated for ' . ($user->name ?? $user->email));
    }
}

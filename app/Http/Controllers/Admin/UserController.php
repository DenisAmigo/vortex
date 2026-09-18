<?php
/**
 * @author denis.chernonozhkin
 * @Date 18.09.2026 15:09
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')
            ->orderBy('id', 'asc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }
}

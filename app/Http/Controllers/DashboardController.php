<?php

namespace App\Http\Controllers;

use App\Enums\UserEnums\UserRole;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === UserRole::ADMIN) {
            return view('dashboards.admin');
        }

        if ($user->role === UserRole::FREELANCER) {
            return view('dashboards.freelancer');
        }

        abort(403);
    }
}

?>

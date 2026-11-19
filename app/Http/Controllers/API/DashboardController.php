<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Project;
use App\Models\Contact;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $data = array();
        $data['totalProject'] = Project::count();
        $data['totalContact'] = Contact::count();
        $data['totalCategory'] = Category::count();
        $data['totalUnreadContact'] = Contact::where('is_read', 0)->count();
        $data['totalReadContact'] = Contact::where('is_read', 1)->count();
        return response()->json([
            'data' => $data
        ]);
    }
}

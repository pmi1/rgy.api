<?php

namespace App\Http\Controllers\API;

use App\Exceptions\APIException;
use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use App\User;
use App\Order;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class DriverController extends APIController
{
    public function list()
    {
        $drivers = DB::table('driver')
            ->get();
        return response()->json(['success' => $drivers], $this->successStatus, [], JSON_NUMERIC_CHECK);
    }
}